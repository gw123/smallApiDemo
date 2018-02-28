<?php
/***
 * 入口文件
 */
define('ENTRY_FILENAME', str_replace("\\", "/", __FILE__ ));
define('APP_PATH', dirname(__FILE__) );
define('ROOT_PATH', dirname(APP_PATH) );

define('VENDOR_PATH',  ROOT_PATH.'/vendor');
define('CORE_PATH',  VENDOR_PATH.'/smallApi');

/**
 * 定义服务器是否开启代理模式 方便定位客户端真实IP，
 * 如果没有使用nginx代理 请注释掉下面的语句
 */
define('SERVER_AGENT', 'nginx');

//加载框架， 初始化server服务器
require(CORE_PATH.'/core/All.php');










