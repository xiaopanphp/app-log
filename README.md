# AppLog - CRM 业务日志组件 

主要用于记录业务日志

## 安装

Install the latest version with

```bash
$ composer require jiedian/app-log:1.0.3
```

## 使用方法

```php
<?php

use Jiedian\AppLog\Log;

// 写入日志
Log::getInstance('app')->info('testmessage');
```

## 说明

- 需要通过传递tag（在/Config/MNLogger.php中获取）获取对应的日志实例来写入日志
- 支持info、debug、notice、warning、error、critical、alert、emergency方法，对应不同的日志级别
- tag标识不同的日志类型，比如请求日志、响应日志、数据库日志等
- 日志按天生成
- 业务日志配置如下：

```php
<?php

namespace Config;

/**
 * Class MNLogger .
 */
class MNLogger
{
	public $appLog = array (
   		'on'  => true, //标识该项目是否启用业务日志
    		'app' => 'api-test', //标识项目名称
    		'tag' => array('app' => '/home/logs/app.log'), //日志标签列表，key=>value形式，key标识类型，value标识日志文件全路径
	);
}
```
