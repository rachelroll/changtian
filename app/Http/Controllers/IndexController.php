<?php

namespace App\Http\Controllers;

use App\OrderItem;
use App\SourceCode;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class IndexController extends Controller
{

    public function sourceCode($id)
    {
        $user = session('wechat.oauth_user.default'); // 拿到授权用户资料
        $source_code = $id;
        $hashids = new Hashids('ross',10,'123456789ABCDEFGH');
        $id = $hashids->decode($id);
        if ($id) {
            $id = Arr::first($id);
            $order_item = OrderItem::find($id);
            if ($order_item) {
                SourceCode::create([
                    'nickname'=>$user->nickname,
                    'avatar'=>$user->avatar,
                    'openid'=>$user->id,
                    'source_code_id'=>$id,
                ]);
                $source_code_model = SourceCode::where('source_code_id', $id)->orderBy('id','DESC')->limit(5)->get();
                return view('m.index.source-code',compact('order_item','source_code','source_code_model'));
            }
            echo '错误参数';
            die;

        } else {
            echo '参数错误';
            die;
        }

    }
}
