<?php

namespace App\Http\Controllers\api;

use App\Good;
use App\Http\Controllers\Controller;
use App\Order;
use App\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $goods_infos = $request->goodsJsonStr;

        $amount = 0;

        $order_id = DB::table('orders')->insertGetId(
            [
                'username' => $request->username,
                'contact' => $request->contact,
                'address' => $request->address,
                'comments' => $request->comments,
            ]
        );

        foreach (json_decode($goods_infos) as $goods_info) {
            $good_id = $goods_info->goodsId;
            $quantity = $goods_info->number;

            $good_info = Good::find($good_id);

            OrderItem::create([
                'order_id' => $order_id,
                'good_id' => $good_id,
                'quantity' => $quantity,
                'price' => $good_info->price,
                'name' => $good_info->name,
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
}
