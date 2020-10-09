<?php
/**
 * 业务日志抽象类
 */
namespace Jiedian\AppLog\Logger;

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
    

    public $config = [];

    public $delimiter = '>';

    /**
     * 初始化
     */
    public function __construct()
    {
        // laravel 环境
        if (class_exists('\LaravelPhpClient\Facades\PhpClient')) {
            $mnloggerConfig = config('mnlogger', array());
        } else {
            $mnloggerConfig = (array) new \Config\MNLogger;
        }
        $this->config = !empty($mnloggerConfig['appLog']) ? $mnloggerConfig['appLog'] : array();
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
