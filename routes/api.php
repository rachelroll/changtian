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
    // ��Ʒ����
    Route::get('/shop/goods/detail', 'GoodsController@show');
    // ��Ʒ�б�
    Route::post('/shop/goods/list', 'GoodsController@index');

    // ���з���
    Route::get('/shop/goods/category/all', 'CategoryController@index');

    // ���ɶ���
    Route::post('/order/create', 'OrderController@create');

    // �ֲ�ͼ
    Route::get('/banner/list', 'BannerController@index');
    // �û����
    Route::post('/user/wxapp/login', 'UserController@login');
});