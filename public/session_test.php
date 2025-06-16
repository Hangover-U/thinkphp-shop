<?php
// 显示所有错误
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 引入ThinkPHP框架
require __DIR__ . '/../vendor/autoload.php';

// 启动会话
$app = new \think\App();
$http = $app->http;
$response = $http->run();

// 输出Session信息
echo "<h1>Session测试</h1>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session数据:\n";
print_r(\think\facade\Session::all());
echo "</pre>";

// 如果没有用户ID，尝试设置一个测试值
if (!\think\facade\Session::has('user_id')) {
    echo "<p>设置测试Session值...</p>";
    \think\facade\Session::set('test_key', 'test_value_' . time());
    echo "<p>请刷新页面查看是否保存成功</p>";
}