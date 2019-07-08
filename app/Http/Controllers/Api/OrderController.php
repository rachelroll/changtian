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

        $user_id = Redis::get($token);
        if (!$user_id) {
            return [
                'original_token' => $request->token,
                'token' => $token,
                'user_id' => $user_id,
                'code' => 202,
                'msg' => '请登录'
            ];
        }

        $goods_infos = $request->goodsJsonStr;

        $goods_infos = trim($goods_infos, '"');
        $amount = 0;

        $comments = $request->comments ? $request->comments : '';
        //订单编号
        $order_sn = date('YmdHis') . (time() + $user_id);
        $order_id = DB::table('orders')->insertGetId(
            [
                'username' => $request->linkMan,
                'contact' => $request->mobile,
                'address' => $request->address,
                'provinceId' => $request->provinceId,
                'cityId' => $request->cityId,
                'districtId' => $request->districtId,
                'comments' => $comments,
                'order_sn' => $order_sn,
                'user_id' => $user_id,
                'created_at' => now(),
                'updated_at' => now(),
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

    public function list(Request $request)
    {
        $token = $request->token;
        $user_id = Redis::get($token);
        if (!$user_id) {
            return [
                'code' => 202,
                'msg' => '请登录'
            ];
        }

        $status = $request->status;

        $orders = Order::withCount('orderItem')->where('user_id', $user_id)->where('status', $status)->get();

        $orderLists = [];
        $goodsMap = [];

        foreach ($orders as $key => $order) {
            switch($order->status){
                case 0:
                    $statusStr = '待支付';
                    break;
                case 1:
                    $statusStr = '待发货';
                    break;
                case 2:
                    $statusStr = '待收货';
                    break;
                case 3:
                    $statusStr = '待评价';
                    break;
                case 4:
                    $statusStr = '已完成';
                    break;
            }

            $orderLists[$key]['amount'] = $order->amount;
            $orderLists[$key]['dateAdd'] = $order->created_at;
            $orderLists[$key]['dateClose'] = $order->created_at;
            $orderLists[$key]['goodsNumber'] = $order->orderItems_count;
            $orderLists[$key]['hasRefund'] = $order->hasRefund;
            $orderLists[$key]['id'] = $order->id;
            $orderLists[$key]['isPay'] = $order->isPay;
            $orderLists[$key]['orderNumber'] = $order->order_sn;
            $orderLists[$key]['remark'] = $order->remark;
            $orderLists[$key]['status'] = $order->status;
            $orderLists[$key]['statusStr'] = $statusStr;
            $orderLists[$key]['userId'] = $user_id;

            $orderItems = OrderItem::where('order_id', $order->id)->get();
            foreach ($orderItems as $key => $item) {
                $goodsMap[$order->id][$key]['amount'] = $order->amount;
                $goodsMap[$order->id][$key]['goodsId'] = $item->good_id;
                $goodsMap[$order->id][$key]['goodsName'] = $item->name;
                $goodsMap[$order->id][$key]['id'] = $item->id;
                $goodsMap[$order->id][$key]['number'] = $item->quantity;
                $goodsMap[$order->id][$key]['orderId'] = $item->order_id;
                $goodsMap[$order->id][$key]['pic'] = config('filesystems.disks.oss.cdnDomain') . '/' . $item->cover;
                $goodsMap[$order->id][$key]['userId'] = $item->user_id;
            }
        }

        return [
            'code' => 0,
            'data' => [
                'orderList' => $orderLists,
                'goodsMap' => $goodsMap,
            ],
            'msg' => 'success',
        ];
    }

    public function statistics(Request $request)
    {
        $token = $request->token;
        $user_id = Redis::get($token);
        if (!$user_id) {
            return [
                'code' => 202,
                'msg' => '请登录'
            ];
        }

        $arr = [0, 0, 0, 0, 0, 0];
        $orders = Order::where('user_id', $user_id)->get();
        foreach ($orders as $order) {
            $arr[$order->status] += 1;
        }

        return [
            'code' => 0,
            'data' => [
                "count_id_no_reputation"=> $arr[3],
                "count_id_no_transfer" => $arr[1],
                "count_id_close" => $arr[5],
                "count_id_no_pay" => $arr[0],
                "count_id_no_confirm" => $arr[2],
                "count_id_success" => $arr[4]
            ],
            'msg' => 'success',
        ];
    }
}
