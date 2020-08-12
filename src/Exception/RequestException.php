<?php

namespace Jiedian\AppLog\Exception;

class RequestException extends \Exception
{
	CONST TAG_FORMAT_ERROR = 100000;
	CONST LOG_OPTION_ERROR = 100001;
	CONST TAG_DENY_ERROR = 100002;

	public static $errorMap = array(
		self::TAG_FORMAT_ERROR => '日志标签格式错误',
		self::LOG_OPTION_ERROR => '缺少业务日志配置',
		self::TAG_DENY_ERROR   => '日志标签不被允许',
	);

	public function __construct($code)
	{
		$message = !empty(self::$errorMap[$code]) ? self::$errorMap[$code] : '';
		parent::__construct($message, $code);
	}
}
