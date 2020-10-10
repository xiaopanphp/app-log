<?php
/**
 * 业务日志
 */
namespace Jiedian\AppLog;

use Jiedian\AppLog\Exception\RequestException;
use Jiedian\AppLog\Logger\Monolog;

class Log
{
    private static $instanceArr = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance($tag)
    {
        if (!isset(self::$instanceArr[$tag])) {
            self::$instanceArr[$tag] = new Monolog($tag);
        }
        return self::$instanceArr[$tag];
    }
}
