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

    // banner
    Route::get('/banner/list', 'BannerController@index');

    // 用户相关
    Route::post('/user/wxapp/login', 'UserController@login');
});