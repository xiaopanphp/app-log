<?php
/**
 * 业务日志
 */
namespace Jiedian\AppLog;

use Jiedian\AppLog\Exception\RequestException;

class Log
{
    /**
     * 默认类名
     */
    const DEFAULT_CLASS   = 'default';

    /**
     * 默认方法名
     */
    const DEFAULT_FUNC    = 'default';

    /**
     * 业务日志配置
     * array('on' => true, 'app' => 'crm-service', 'logdir' => '/home/logs/app_log','max_length' => 500,'allow_tags' => array('adjustPrice' => array('enable' => true)));
     */
    private $appLogOptions;

    /**
     * 异常日志配置
     */
    private $exceptionLogOptions;

    /**
     * 实例
     */
    private static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 写入业务日志
     * \Jiedian\AppLog\Log::getInstance()->writeLog('test','adjustPrice');
     * @param  mixed  $content 日志内容
     * @param  string $tag     业务日志标签，需在dove配置中存在
     * @return void
     */
    public function writeLog($content, $tag)
    {
        try {
            $trace     = debug_backtrace(2, 2); //获取追溯信息
            $traceInfo = !empty($trace[1]) ? $trace[1] : array(); //获取调用者信息

            //设置日志相关参数
            $this->setLogOptions();
            //校验tag标签能否写入
            $this->checkTagPermisson($tag);
            //获取内容
            $content = $this->getContent($content);
            //校验字符长度
            $this->checkContentLength($content);
            //获取调用者信息
            $callerInfo = $this->getCallerInfo($traceInfo);
            //获取项目名
            $project    = $this->getProject();
            //获取日志文件
            $file       = $this->getLogFile();

            $logData = array(
                'project'    => $project,
                'class_name' => $callerInfo['className'],
                'func_name'  => $callerInfo['funcName'],
                'tag'        => $tag,
                'log_time'   => date('Y-m-d H:i:s'),
                'content'    => $content,
            );
            file_put_contents($file, json_encode($logData, 320).PHP_EOL, FILE_APPEND | LOCK_EX);
        } catch (RequestException $e) {
            $this->writeExceptionLog($e);
        } catch (\Exception $e) {
            $this->writeExceptionLog($e);
        }
    }

    /**
     * 写入owl异常日志
     * @param  object $e 异常信息
     * @return void
     */
    private function writeExceptionLog($e)
    {
        try {
            if (in_array($e->getCode(), array(RequestException::TAG_DENY_ERROR, RequestException::STATUS_CLOSED_ERROR))) {
                //主动关闭标签后，防止后续日志继续写入owl
                return;
            }
            \MNLogger\EXLogger::setUp($this->exceptionLogOptions);
            \MNLogger\EXLogger::instance()->log($e);
        } catch (\Exception $ex) {
            //do nothing
        }
    }

    /**
     * 校验标签权限
     * @param  string $tag 业务日志标签，需在deve配置中存在
     * @return void
     */
    private function checkTagPermisson($tag)
    {
        if (empty($tag) || !is_string($tag)) {
            throw new RequestException(RequestException::TAG_FORMAT_ERROR);
        }
        $appLogOptions = $this->appLogOptions;
        if (empty($appLogOptions['on'])) {
            throw new RequestException(RequestException::STATUS_CLOSED_ERROR);
        }

        if (!isset($appLogOptions['logdir']) || !isset($appLogOptions['allow_tags']) || !isset($appLogOptions['app'])) {
            throw new RequestException(RequestException::LOG_OPTION_ERROR);
        }

        $allowTags = !empty($appLogOptions['allow_tags']) ? $appLogOptions['allow_tags'] : array();
        if (empty($allowTags[$tag]['enable'])) {
            throw new RequestException(RequestException::TAG_DENY_ERROR);
        }
    }

    /**
     * 设置日志相关参数
     * @return array
     */
    private function setLogOptions()
    {
        // laravel 环境
        if (class_exists('\LaravelPhpClient\Facades\PhpClient')) {
            $mnloggerConfig = config('mnlogger', array());
        } else {
            $mnloggerConfig = (array) new \Config\MNLogger;
        }
        $appLogOptions       = !empty($mnloggerConfig['appLog']) ? $mnloggerConfig['appLog'] : array();
        $exceptionLogOptions = !empty($mnloggerConfig['exception']) ? array('exception' => $mnloggerConfig['exception']) : array();

        $this->appLogOptions       = $appLogOptions;
        $this->exceptionLogOptions = $exceptionLogOptions;
    }

    /**
     * 校验字符长度
     */
    private function checkContentLength($content)
    {
        if (!empty($this->appLogOptions['max_length']) && mb_strlen($content) > $this->appLogOptions['max_length']) {
            throw new RequestException(RequestException::CONTENT_LENGTH_ERROR);
        }
    }

    /**
     * 获取项目名称
     * @return string
     */
    private function getProject()
    {
        return $this->appLogOptions['app'];
    }

    /**
     * 获取日志内容
     * @param  mixed $content 日志内容
     * @return string
     */
    private function getContent($content)
    {
        if (is_array($content)) {
            $content = json_encode($content, 320);
        }
        if (!is_string($content) && !is_numeric($content) && !is_bool($content)) {
            throw new RequestException(RequestException::CONTENT_FORMAT_ERROR);
        }
        return $content;
    }

    /**
     * 获取调用方信息
     * @param  array $traceInfo 调用方数据
     * @return array
     */
    private function getCallerInfo($traceInfo)
    {
        //获取调用者类名、方法名
        $className = !empty($traceInfo['class']) && is_string($traceInfo['class']) ? str_replace('\\\\', '\\', $traceInfo['class']) : self::DEFAULT_CLASS;
        $funcName  = !empty($traceInfo['function']) && is_string($traceInfo['function']) ? $traceInfo['function'] : self::DEFAULT_FUNC;
        return array('className' => $className, 'funcName' => $funcName);
    }

    /**
     * 获取日志文件
     * @return string
     */
    private function getLogFile()
    {
        $path = $this->appLogOptions['logdir'];
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $filename = date("Ymd").".log";
        return $path.DIRECTORY_SEPARATOR.$filename;
    }
}
