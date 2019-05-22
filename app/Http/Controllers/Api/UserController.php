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

        //����appid
        $appid = env('APPID');
        //����appscret
        $secret = env('APPSECRET');
        //api�ӿ�
        $api = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";

        $res = Utils::curl($api);

        $res = json_decode($res);

        $openid = $res->openid;
        $session_key = $res->session_key;

        // ���� openid ���û������Ƿ�������û�
        $user_id = optional(User::where('openid', $openid)->first())->id;
        if ($user_id) {
            // ���û� ID �������� token
            $token = md5($user_id, config('salt'));
            Redis::set($token, $user_id); // ���� session

            return [
                'code' => 0,
                'data' => [
                    'token' => $token
                ]
            ];
        }
        else{
            // �� session_key �� openid �������ݿ�, �������û� id
            $id = DB::table('users')->insertGetId(
                ['session_key' => $session_key,
                 'openid' => $openid,
                 'created_at' => now(),
                 'updated_at' => now()]
            );
            // ����û�����ɹ�
            if ($id) {
                // ���û� ID �������� token
                $token = md5($id, config('salt'));

                Redis::set($token, $id); // ���� session
                return $token;
            }else {
                return [
                    'code' => 202,
                    'msg' => 'error'
                ];
            }
        }
    }
}
