<?php
/**
 * monolog
 */
namespace Jiedian\AppLog\Logger;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Jiedian\AppLog\Exception\RequestException;
use AppLog;

class Monolog extends AppLog
{
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
     * 系统名称
     * @var string
     */
    private $systemName;

    /**
     * 主机名称
     * @var string
     */
    private $hostName;

    /**
     * 记录器实例
     * @var object
     */
    private $logger;

    /**
     * 记录器名称
     * @var string
     */
    private $channel;

    /**
     * 初始化
     */
    public function __construct($tag)
    {
        //获取业务日志配置
        parent::__construct();
        $config = !empty($this->config[$tag]) ? $this->config[$tag] : [];
        if (empty($config['logFile'])) {
            throw new RequestException(RequestException::LOG_OPTION_ERROR);
        }
        $logFile = $config['logFile'];
        //设置公共参数
        $this->setCommonData();

        $logger    = new Logger($tag);
        $stream    = new RotatingFileHandler($logFile, Logger::INFO); //每天一个文件
        $formatter = new LineFormatter($this->outputFormat, $this->dateFormat); //自定义日志格式
        $stream->setFormatter($formatter);
        $logger->pushHandler($stream);
        $this->logger  = $logger;
        $this->channel = $tag;
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
     * 设置公共数据
     * @return void
     */
    private function setCommonData()
    {
        $this->dateFormat   = 'c'; //2020-10-09T11:44:21+08:00带时区格式
        $this->outputFormat = "[%datetime%] {$this->delimiter} %message% {$this->delimiter} %context%\n";
        $this->systemName   = 'crm-api';
        $this->hostName     = 'panqiang';
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
        if ($this->check()) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            array_shift($trace);
            $file = !empty($trace[0]['file']) ? $trace[0]['file'] : '';
            $line = !empty($trace[0]['line']) ? $trace[0]['line'] : '';
            $traceId = '2009';
            
            $message = "{$this->systemName} {$this->delimiter} {$this->channel} {$this->delimiter} {$level} {$this->delimiter} {$this->hostName} {$this->delimiter} {$file}"."[{$line}] {$this->delimiter} {$traceId} {$this->delimiter} {$message}";
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * 检查日志内容格式
     * @param  string $message 日志内容
     * @return bool
     */
    private function check($message)
    {
        if (!is_string($message) && !is_numeric($message) && !is_bool($message)) {
            return false;
        }
        return true;
    }
}
