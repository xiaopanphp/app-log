<?php
/**
 * monolog
 */
namespace Jiedian\AppLog\Logger;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Jiedian\AppLog\Exception\RequestException;
use Jiedian\AppLog\Logger\AppLog;

class Monolog extends AppLog
{
    /**
     * 默认记录器实例
     * @var string
     */
    private $defaultLogger = 'default';
    /**
     * 输出格式
     * @var string
     */
    private $outputFormat;

    /**
     * 日期格式
     * @var string
     */
    private $dateFormat;

    /**
     * 记录器实例
     * @var object
     */
    private $logger;

    /**
     * 初始化
     */
    public function __construct($tag)
    {
        try {
            //获取业务日志配置
            parent::__construct($tag);
            //获取日志文件
            $logFile = $this->tagConfig[$tag];
            //设置日志格式
            $this->setLogFormat();
            //获取monolog日志实例
            $logger    = new Logger($tag);
            //申请一个按天分的日志文件处理器
            $stream    = new RotatingFileHandler($logFile, Logger::DEBUG); //每天一个文件
            //定义日志文件格式
            $formatter = new LineFormatter($this->outputFormat, $this->dateFormat); //自定义日志格式
            //日志格式应用到处理器
            $stream->setFormatter($formatter);
            //日志处理器放入处理器栈
            $logger->pushHandler($stream);
        } catch (RequestException $e) {
            $logger = new Logger($this->defaultLogger);
            $this->isWrite = false;
        }
        $this->logger  = $logger;
    }

    /**
     * debug写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    public function debug($message, $context = [])
    {
        $this->writeLog(self::DEBUG, $message, $context);
    }
    
    /**
     * info写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    public function info($message, $context = [])
    {
        $this->writeLog(self::INFO, $message, $context);
    }

    /**
     * notice写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    public function notice($message, $context = [])
    {
        $this->writeLog(self::NOTICE, $message, $context);
    }

    /**
     * warning写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    public function warning($message, $context = [])
    {
        $this->writeLog(self::WARNING, $message, $context);
    }

    /**
     * error写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    public function error($message, $context = [])
    {
        $this->writeLog(self::ERROR, $message, $context);
    }

    /**
     * critical写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    public function critical($message, $context = [])
    {
        $this->writeLog(self::CRITICAL, $message, $context);
    }

    /**
     * alert写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    public function alert($message, $context = [])
    {
        $this->writeLog(self::ALERT, $message, $context);
    }

    /**
     * emergency写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    public function emergency($message, $context = [])
    {
        $this->writeLog(self::EMERGENCY, $message, $context);
    }

    /**
     * 设置日志格式
     * @return void
     */
    private function setLogFormat()
    {
        $this->dateFormat   = 'c'; //2020-10-09T11:44:21+08:00带时区格式
        $this->outputFormat = "[%datetime%]{$this->delimiter}%message%{$this->delimiter}%context%\n";
    }

    /**
     * monlog写日志方法
     * @param string $level 日志级别
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    private function writeLog($level, $message, $context)
    {
        if ($this->check($message)) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            array_shift($trace);
            $file = !empty($trace[0]['file']) ? $trace[0]['file'] : '';
            $line = !empty($trace[0]['line']) ? $trace[0]['line'] : '';
            
            $message = "{$this->systemName}{$this->delimiter}{$this->channel}{$this->delimiter}{$level}{$this->delimiter}{$this->hostName}{$this->delimiter}{$file}"."[{$line}]{$this->delimiter}{$this->traceId}{$this->delimiter}{$message}";
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * 检查日志是否能写入
     * @param  string $message 日志内容
     * @return bool
     */
    private function check($message)
    {
        if (!$this->isWrite) {
            return false;
        }
        if (!is_string($message) && !is_numeric($message) && !is_bool($message)) {
            return false;
        }
        return true;
    }
}
