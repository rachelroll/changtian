<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api'], function () {

    // 商品详情
    Route::get('/shop/goods/detail', 'GoodsController@show');
    // 商品列表
    Route::post('/shop/goods/list', 'GoodsController@index');
    // 产品分类
    Route::get('/shop/goods/category/all', 'CategoryController@index');

    // 创建订单
    Route::post('/order/create', 'OrderController@create');
    // 我的订单列表
    Route::get('/orders', 'OrderController@index');
    // 订单列表
    Route::post('/order/list', 'OrderController@list');
    // 订单状态
    Route::get('/order/statistics', 'OrderController@statistics');
    // 订单详情
    Route::get('order/detail', 'OrderController@detail');
    // 确认收货
    Route::post('order/delivery', 'OrderController@delivery');

    // banner
    Route::get('/banner/list', 'BannerController@index');

    // 用户相关
    Route::post('/user/wxapp/login', 'UserController@login');

    // 获取默认地址
    Route::get('/user/shipping-address/default', 'AddressController@default');
    // 获取地址列表
    Route::get('/user/shipping-address/list', 'AddressController@index');
    // 新增收货地址
    Route::post('/user/shipping-address/add', 'AddressController@add');
    // 更新收货地址
    Route::post('/user/shipping-address/update', 'AddressController@update');
    // 获取省份信息
    Route::get('/common/region/v2/province', 'AddressController@province');
    // 获取地址详细信息
    Route::get('/user/shipping-address/detail', 'AddressController@detail');


    // 请求微信统一下单接口
    Route::post('/pay/wx/wxapp', 'PaymentController@place_order')->name('api.payment.place_order');
    // 请求微信接口, 查看订单支付状态
    Route::get('/payment/paid', 'PaymentController@paid')->name('api.payment.paid');
    // 接收微信的通知
    Route::post('/payment/notify', 'paymentController@notify');
});