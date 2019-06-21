<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeChatController extends Controller
{
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $app = app('wechat.official_account');
        $app->server->push(function($message){
            return "欢迎关注 宁夏昌田农业发展有限公司!";
        });

        return $app->server->serve();
    }

    public function setMenu()
    {
        $app = app('wechat.official_account');
        $buttons = [
            [
                'type' => 'click',
                'name' => '今日歌曲',
                'key'  => 'V1001_TODAY_MUSIC'
            ],
            [
                'name'       => '菜单',
                'sub_button' => [
                    [
                        'type' => 'view',
                        'name' => '搜索',
                        'url'  => 'http://www.soso.com/'
                    ],
                    [
                        'type' => 'miniprogram',
                        'name' => 'wxa',
                        'url' => 'http://mp.weixin.qq.com',
                        'appid' => 'wx286b93c14bbf93aa',
                        'pagepath' => 'pages/lunar/index',
                    ],
                    [
                        'type' => 'click',
                        'name' => '赞一下我们',
                        'key' => 'V1001_GOOD'
                    ],
                ],
            ],
        ];
        $app->menu->create($buttons);
    }
}
