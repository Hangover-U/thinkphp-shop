<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::get('think', function () {
    return 'hello,ThinkPHP6!';
});

Route::get('hello/:name', 'index/hello');

// 首页
Route::get('/', 'Index/index');

// 商品相关路由
Route::get('category/:id', 'Index/category');
Route::get('search', 'Index/search');
Route::get('product/:id', 'Index/detail');
Route::get('products', 'Index/list');
Route::get('recommend', 'Index/recommend');

// 用户相关路由
Route::group('user', function () {
    Route::get('login', 'User/login');
    Route::post('login', 'User/doLogin');
    Route::get('register', 'User/register');
    Route::post('register', 'User/doRegister');
    Route::get('logout', 'User/logout');
    Route::get('center', 'User/center');
    Route::post('update_info', 'User/updateInfo');
    Route::post('update_password', 'User/updatePassword');
    Route::get('list', 'User/list');
});

// 购物车相关路由
Route::group('cart', function () {
    Route::get('/', 'Cart/index');
    Route::post('add', 'Cart/add');
    Route::post('update', 'Cart/update');
    Route::post('updateQuantity', 'Cart/updateQuantity');
    Route::post('remove', 'Cart/remove');
    Route::post('clear', 'Cart/clear');
    Route::post('update_selected', 'Cart/updateSelected');
});

// 订单相关路由
Route::group('order', function () {
    Route::get('/', 'Order/index');
    Route::get('detail/:id', 'Order/detail');
    Route::get('create', 'Order/create');
    Route::post('submit', 'Order/submit');
    Route::post('pay', 'Order/pay');
    Route::post('cancel', 'Order/cancel');
    Route::post('complete', 'Order/complete');
});

// 管理员控制台相关路由
Route::group('admin', function () {
    Route::get('/', 'Admin/index');
    Route::get('product_list', 'Admin/productList');
    Route::get('add_product', 'Admin/addProduct');
    Route::post('do_add_product', 'Admin/doAddProduct');
    Route::get('edit_product/:id', 'Admin/editProduct');
    Route::post('do_edit_product', 'Admin/doEditProduct');
    Route::post('delete_product', 'Admin/deleteProduct');
    Route::get('order_list', 'Admin/orderList');
    Route::get('order_detail/:id', 'Admin/orderDetail');
    Route::post('ship', 'Admin/ship');
    Route::get('user_list', 'Admin/userList');
    Route::get('add_user', 'Admin/addUser');
    Route::post('do_add_user', 'Admin/doAddUser');
    Route::get('edit_user/:id', 'Admin/editUser');
    Route::post('do_edit_user', 'Admin/doEditUser');
    Route::post('delete_user', 'Admin/deleteUser');
});
