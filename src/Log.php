<?php
/**
 * 业务日志
 */
namespace Jiedian\AppLog;

use Jiedian\AppLog\Exception\RequestException;
use Jiedian\AppLog\Logger\Monolog;

class Log
{
    /**
     * 业务日志配置
     */
    private $appLogOptions;

    private static $instanceArr = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance($tag, $logType = 'Monolog')
    {
        if (!isset(self::$instanceArr[$tag])) {
            self::$instanceArr[$tag] = new $logType($tag);
        }
        return self::$instance[$tag];
    }
}
