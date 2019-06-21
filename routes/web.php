<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::group(['middleware' => ['web', 'wechat.oauth']], function () {
    Route::get('/source-code/{id}','IndexController@sourceCode');
    Route::get('/user', function () {
        $user = session('wechat.oauth_user.default'); // 拿到授权用户资料

        dd($user);
    });
});

Route::any('/wechat', 'WeChatController@serve');
Route::any('/wechat/set-menu', 'WeChatController@setMenu');
