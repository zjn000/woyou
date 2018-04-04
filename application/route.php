<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

//如果有定义绑定后台模块则禁用路由规则 
if (Route::getBind('module') == 'admin')
    return [];

//优惠券
Route::group('coupon',[
    'list' => 'api/Coupon/get_list',
    'one' => 'api/Coupon/get_best_one',
    'all' => 'api/Coupon/get_all'
],['method' => 'post']);


//用户
Route::group('user', [
    'login' => 'api/Login/index',
    'check_bind' => 'api/User/is_bind',
    'bind' => 'api/User/bind_phone'
], ['method' => 'post']);

//货架
Route::group('shelves', [
    'detail' => 'api/Shelves/detail'
], ['method' => 'post']);


//订单
Route::group('order', [
    'add' => 'api/order.Orderadd/index',
    'detail' => 'api/order.Orderdetail/index',
    'list' => 'api/order.Orderlist/index'
], ['method' => 'post']);


//商品
Route::group('product', [
    'detail' => 'api/product.Productdetail/index',
], ['method' => 'post']);


//支付
Route::group('pay', [
    'index' => ['api/Pay/index', ['method' => 'post']]
]);

//FAQ
Route::group('faq',[
    'feedback' => 'api/Feedback/index',
], ['method' => 'post']);