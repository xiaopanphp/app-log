<?php

namespace Jiedian\AppLog\Exception;

class RequestException extends \Exception
{
    const LOG_OPTION_ERROR = 100000;
    const TAG_OPTION_ERROR = 100001;

    public static $errorMap = array(
        self::LOG_OPTION_ERROR  => '缺少业务日志配置',
        self::TAG_OPTION_ERROR  => '业务日志TAG不存在',
    );

    public function __construct($code)
    {
        $message = !empty(self::$errorMap[$code]) ? self::$errorMap[$code] : '';
        parent::__construct($message, $code);
    }
}
