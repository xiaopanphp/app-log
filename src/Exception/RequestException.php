<?php

namespace Jiedian\AppLog\Exception;

class RequestException extends \Exception
{
    const TAG_FORMAT_ERROR     = 100000;
    const LOG_OPTION_ERROR     = 100001;
    const TAG_DENY_ERROR       = 100002;
    const CONTENT_FORMAT_ERROR = 100003;
    const CONTENT_LENGTH_ERROR = 100004;
    const STATUS_CLOSED_ERROR  = 100005;

    public static $errorMap = array(
        self::TAG_FORMAT_ERROR     => '日志标签格式错误',
        self::LOG_OPTION_ERROR     => '缺少业务日志配置',
        self::TAG_DENY_ERROR       => '日志标签不被允许',
        self::CONTENT_FORMAT_ERROR => '内容格式错误',
        self::CONTENT_LENGTH_ERROR => '内容长度超限',
        self::STATUS_CLOSED_ERROR  => '写入状态关闭',
    );

    public function __construct($code)
    {
        $message = !empty(self::$errorMap[$code]) ? self::$errorMap[$code] : '';
        parent::__construct($message, $code);
    }
}
