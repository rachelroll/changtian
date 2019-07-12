<?php

namespace App\Http\Controllers\api;

use App\User;
use App\Utils\Utils;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $code = $request->code;

        //配置appid
        $appid = 'wxd1544fa92688e370';
        //配置appscret
        $secret = 'c7ff047c25a4459e58d2dfd40c79a0da';
        //api接口
        $api = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";

        $res = Utils::curl($api);

        $res = json_decode($res);

        $openid = $res->openid;
        $session_key = $res->session_key;

        // 根据 openid 查用户表里是否有这个用户
        $user_id = optional(User::where('openid', $openid)->first())->id;
        if ($user_id) {
            // 把用户 ID 加密生成 token
            $token = md5($user_id, config('salt'));
            Redis::set($token, $user_id); // 存入 session

            return [
                'code' => 0,
                'data' => [
                    'token' => $token
                ]
            ];
        }
        else{
            // 把 session_key 和 openid 存入数据库, 并返回用户 id
            $id = DB::table('users')->insertGetId(
                ['session_key' => $session_key,
                 'openid' => $openid,
                 'created_at' => now(),
                 'updated_at' => now()]
            );
            // 如果用户储存成功
            if ($id) {
                // 把用户 ID 加密生成 token
                $token = md5($id, config('salt'));

                Redis::set($token, $id); // 存入 session
                return $token;
            }else {
                return [
                    'code' => 202,
                    'msg' => 'error'
                ];
            }
        }
    }

    public function detail(Request $request)
    {
        $token = $request->token;


    }
}
