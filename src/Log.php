<?php
/**
 * 业务日志
 */
namespace Jiedian\AppLog;

use Jiedian\AppLog\Exception\RequestException;

class Log
{
    /**
	 * 默认项目名
	 */
	CONST DEFAULT_PROJECT = 'default';

	/**
	 * 默认类名
	 */
	CONST DEFAULT_CLASS   = 'default';

	/**
	 * 默认方法名
	 */
	CONST DEFAULT_FUNC    = 'default';

	/**
	 * 业务日志配置
	 * array('log_path' => '/home/logs/app_log','allow_tags' => array('adjustPrice' => array('enable' => true)));
	 */
	private $appLogOptions;

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
     * @param  string $tag     业务日志标签，需在deve配置中存在
     * @return void
     */
    public function writeLog($content, $tag)
    {
    	try {
    		$trace     = debug_backtrace(2, 2); //获取追溯信息
    		$traceInfo = !empty($trace[1]) ? $trace[1] : array(); //获取调用者信息
    		//校验tag标签能否写入
    		$this->checkTagPermisson($tag);
    		//获取调用者信息
   			$callerInfo = $this->getCallerInfo($traceInfo);
   			//获取项目名
   			$project    = $this->getProject();
   			//获取内容
   			$content    = $this->getContent($content);
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
    		file_put_contents($file, json_encode($logData, 320).PHP_EOL, FILE_APPEND);
    	} catch (RequestException $e) {
   			//todo
    	} catch (\Exception $e) {
    		//todo
    	}
    }

    /**
     * 校验标签权限
     * @param  string $tag     业务日志标签，需在deve配置中存在
     * @return void
     */
    private function checkTagPermisson($tag)
    {
    	if (empty($tag) || !is_string($tag)) {
    		throw new RequestException(RequestException::TAG_FORMAT_ERROR);
    	}
    	$appLogOptions = \Config\Log::$appLogOptions;
    	if (!isset($appLogOptions['log_path']) || !isset($appLogOptions['allow_tags'])) {
    		throw new RequestException(RequestException::LOG_OPTION_ERROR);
    	}
    	$this->appLogOptions = $appLogOptions;

    	$allowTags = !empty($appLogOptions['allow_tags']) ? $appLogOptions['allow_tags'] : array();
    	if (!isset($allowTags[$tag]['enable']) || empty($allowTags[$tag]['enable'])) {
    		throw new RequestException(RequestException::TAG_DENY_ERROR);
    	}
    }

    /**
     * 获取项目名称
     * @return string
     */
    private function getProject()
    {
    	return defined('JM_APP_NAME') ? JM_APP_NAME : self::DEFAULT_PROJECT;
    }

    /**
     * 获取日志内容
     * @param  mixed $content 日志内容
     * @return string
     */
    private function getContent($content)
	{
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
        $path = $this->appLogOptions['log_path'];
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $filename = date("Ymd").".log";
        return $path.DIRECTORY_SEPARATOR.$filename;
    }
}
