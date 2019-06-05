<?php

namespace App\Http\Controllers\api;

use App\Good;
use App\Http\Controllers\Controller;
use App\Order;
use App\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Http\Resources\OrderItem as OrderItemResource;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $token = $request->token;
        if (!$token) {
            return [
                'code' => 202,
                'msg' => '请登录'
            ];
        }

        $user_id = Redis::get($token);

        $goods_infos = $request->goodsJsonStr;

        $goods_infos = trim($goods_infos, '"');
        $amount = 0;

        $order_id = DB::table('orders')->insertGetId(
            [
                'username' => $request->username,
                'contact' => $request->contact,
                'address' => $request->address,
                'comments' => $request->comments,
                'user_id' => $user_id,
            ]
        );

        foreach (json_decode($goods_infos) as $goods_info) {
            $good_id = $goods_info->goodsId;
            $quantity = $goods_info->number;

            $good_info = Good::find($good_id);
            $pictures = $good_info->pictures;

            OrderItem::create([
                'order_id' => $order_id,
                'good_id' => $good_id,
                'quantity' => $quantity,
                'price' => $good_info->price,
                'name' => $good_info->name,
                'user_id' => $user_id,
                'cover' => $pictures[0]
            ]);

            $amount += $good_id * $quantity;
        }

        Order::where('id', $order_id)->update([
            'amount' => $amount
        ]);

        return [
            'code' => 0,
            'msg' => '请等待工作人员与您联系'
        ];
    }

    public function index(Request $request)
    {
        $token = $request->token;

        $user_id = Redis::get($token);

        $orders = OrderItem::where('user_id', $user_id)->get();

        foreach ($orders as &$order) {
            $order->cover = config('filesystems.disks.oss.cdnDomain') . '/' . $order->cover;
        }

        return OrderItemResource::collection($orders);
    }
}
