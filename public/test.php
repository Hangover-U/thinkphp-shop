<?php
// 显示所有错误
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 引入ThinkPHP框架
require __DIR__ . '/../vendor/autoload.php';

// 执行应用并响应
$http = (new think\App())->http;
$response = $http->run();
$response->send();

$http->end($response); 