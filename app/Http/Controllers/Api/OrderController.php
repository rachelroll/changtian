<?php

namespace App\Http\Controllers\api;

use App\ChinaArea;
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

        $orders = Order::withCount('orderItems')->where('user_id', $user_id)->where('status', $status)->get();

        $orderLists = [];
        $goodsMap = [];

        foreach ($orders as $key => $order) {
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
            $orderLists[$key]['statusStr'] = Order::STATUS[$order->status];
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

    // 订单详情
    public function detail(Request $request)
    {
        $token = $request->token;
        $user_id = $this->checkTocken($token);

        $order_id = $request->id;

        $order = Order::withCount('orderItems')->where('id', $order_id)->first();

        $orderInfo = [];

        $orderInfo['amount'] = $order->amount;
        $orderInfo['dateAdd'] = date('Y-m-d H:i:s', strtotime($order->created_at));
        $orderInfo['dateClose'] = date('Y-m-d H:i:s', strtotime($order->created_at) + 1800);
        $orderInfo['goodsNumber'] = $order->order_items_count;
        $orderInfo['hasRefund'] = $order->hasRefund;
        $orderInfo['isPay'] = $order->isPay;
        $orderInfo['orderNumber'] = $order->order_sn;
        $orderInfo['remark'] = $order->remark;
        $orderInfo['status'] = $order->status;
        $orderInfo['statusStr'] = Order::STATUS[$order->status];
        $orderInfo['amount'] = $order->amount;
        $orderInfo['userId'] = $order->user_id;

        $provinceStr = ChinaArea::where('code', substr($order->provinceId,0,6))->first()->name;
        $cityStr = ChinaArea::where('code', substr($order->cityId,0,6))->first()->name;
        $districtStr = ChinaArea::where('code', substr($order->districtId,0,6))->first()->name;

        if ($order->trackingNumber) {
            $logistics = [
                'trackingNumber' => $order->trackingNumber,
                'linkMan' => $order->username,
                'mobile' => $order->contact,
                'address' => $order->address,
                'provinceStr' => $provinceStr,
                'cityStr' => $cityStr,
                'areaStr' => $districtStr,
            ];
        } else {
            $logistics = false;
        }


        $order_items = $order->orderItems;

        if ($order_items) {
            $goods = [];
            foreach ($order_items as $key => $order_item) {
                $goods[$key]['amount'] = $order_item->price;
                $goods[$key]['goodsId'] = $order_item->good_id;
                $goods[$key]['goodsName'] = $order_item->name;
                $goods[$key]['id'] = $order_item->id;
                $goods[$key]['number'] = $order_item->quantity;
                $goods[$key]['orderId'] = $order_item->order_id;
                $goods[$key]['pic'] = config('filesystems.disks.oss.cdnDomain') . '/' . $order_item->cover;
                $goods[$key]['userId'] = $order_item->user_id;
            }
        }

        return [
            'code' => 0,
            'data' => [
                'orderInfo' => $orderInfo,
                'goods' => $goods,
                'logistics' => $logistics
            ],
            'msg' => 'success'
        ];
    }

    public function delivery(Request $request)
    {
        $token = $request->token;
        $user_id = $this->checkTocken($token);

        $order_id = $request->orderId;

        $bool = Order::where('id', $order_id)->update([
            'status' => 3
        ]);

        if ($bool) {
            return [
                'code' => 0,
                'msg' => 'success'
            ];
        }
    }

    private function checkTocken($token)
    {
        $user_id = Redis::get($token);
        if (!$user_id) {
            return [
                'code' => 202,
                'msg' => '请登录'
            ];
        } else {
            return $user_id;
        }
    }
}
