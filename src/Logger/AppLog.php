<?php
/**
 * 业务日志抽象类
 */
namespace Jiedian\AppLog\Logger;

use Jiedian\AppLog\Exception\RequestException;

abstract class AppLog
{
    const DEBUG     = 'DEBUG';
    const INFO      = 'INFO';
    const NOTICE    = 'NOTICE';
    const WARNING   = 'WARNING';
    const ERROR     = 'ERROR';
    const CRITICAL  = 'CRITICAL';
    const ALERT     = 'ALERT';
    const EMERGENCY = 'EMERGENCY';
    
    /**
     * 业务日志配置
     * @var array
     */
    public $config = [];

    /**
     * 分隔符
     * @var string
     */
    public $delimiter = ' ';

    /**
     * 记录器名称
     * @var string
     */
    public $channel;

    /**
     * 主机名称
     * @var string
     */
    public $hostName;

    /**
     * 系统名称
     * @var string
     */
    public $systemName;

    /**
     * 是否能写如开关
     * @var bool
     */
    public $isWrite;

    /**
     * 标签配置
     * @var array
     */
    public $tagConfig;


    /**
     * traceId
     * @var string
     */
    public $traceId;

    /**
     * 初始化
     * array (
      'on'  => true,
      'app' => 'api-test',
      'tag' => array('app' => '/home/logs/app.log'),
    );
     */
    public function __construct($tag)
    {
        // laravel 环境
        if (class_exists('\LaravelPhpClient\Facades\PhpClient')) {
            $mnloggerConfig = config('mnlogger', array());
        } else {
            $mnloggerConfig = (array) new \Config\MNLogger;
        }
        //设置记录器
        $this->channel = $tag;
        //读取业务日志
        $this->config  = !empty($mnloggerConfig['appLog']) ? $mnloggerConfig['appLog'] : array();
        //获取业务日志失败
        if (empty($this->config)) {
            throw new RequestException(RequestException::LOG_OPTION_ERROR);
        }
        $this->traceId    = '';
        //获取主机名称
        $this->hostName   = (string) gethostname();
        //获取系统名称
        $this->systemName = !empty($this->config['app']) ? $this->config['app'] : '';
        //获取写入开关
        $this->isWrite    = !empty($this->config['on']) ? true : false;
        //标签配置
        $this->tagConfig  = !empty($this->config['tag']) ? $this->config['tag'] : [];
        if (empty($this->tagConfig[$this->channel])) {
            throw new RequestException(RequestException::TAG_OPTION_ERROR);
        }
    }
    /**
     * debug写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    abstract public function debug($message, $context = []);

    /**
     * info写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    abstract public function info($message, $context = []);

    /**
     * notice写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    abstract public function notice($message, $context = []);

    /**
     * warning写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    abstract public function warning($message, $context = []);

    /**
     * error写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    abstract public function error($message, $context = []);

    /**
     * critical写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    abstract public function critical($message, $context = []);

    /**
     * alert写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    abstract public function alert($message, $context = []);

    /**
     * emergency写日志方法
     * @param string $message 日志内容
     * @param array  $context 上下文参数
     * @return void
     */
    abstract public function emergency($message, $context = []);
}
