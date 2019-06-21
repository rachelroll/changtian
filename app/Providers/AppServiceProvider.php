<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Arr;
use Overtrue\Socialite\User as SocialiteUser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('APP_DEBUG')) {
            $user_arr = [
                'openid'=>'123',
                'nickname'=>'ross',
                'headimgurl'=>'https://via.placeholder.com/200X200/D95353/ccc?text=ross',
            ];
            $user = new SocialiteUser([
                'id' => Arr::get($user_arr, 'openid'),
                'name' => Arr::get($user_arr, 'nickname'),
                'nickname' => Arr::get($user_arr, 'nickname'),
                'avatar' => Arr::get($user_arr, 'headimgurl'),
                'email' => null,
                'original' => [],
                'provider' => 'WeChat',
            ]);
            session(['wechat.oauth_user.default' => $user]); // 同理，`default` 可以更换为您对应的其它配置名
        }

    }
}
