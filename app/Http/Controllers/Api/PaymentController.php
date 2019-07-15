<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Order;
use App\Paylog;
use App\User;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class PaymentController extends Controller
{
    // 请求微信接口的公用配置, 所以单独提出来
    private function payment()
    {
        $config = [
            // 必要配置, 这些都是之前在 .env 里配置好的
            'app_id' => config('wechat.payment.default.app_id'),
            'mch_id' => config('wechat.payment.default.mch_id'),
            'key'    => config('wechat.payment.default.key'),   // API 密钥
            'notify_url' => config('wechat.payment.default.notify_url'),   // 通知地址
        ];
        // 这个就是 easywechat 封装的了, 一行代码搞定, 照着写就行了
        $app = Factory::payment($config);

        return $app;
    }

    // 向微信请求统一下单接口, 创建预支付订单
    public function placeOrder(Request $request)
    {
        $token = $request->token;
        $user_id = Redis::get($token);
        if (!$user_id) {
            return [
                'code' => 202,
                'msg' => '请登录'
            ];
        }

        $openid = optional(User::where('id', $user_id)->first())->openid;
        $id = $request->id;
        // 根据订单 id 查出价格
        $order_price = optional(Order::where('id', $id)->first())->pirce;
        $order_sn = optional(Order::where('id', $id)->first())->order_sn;

        // 创建 Paylog 记录
        PayLog::create([
            'appid' => config('wechat.payment.default.app_id'),
            'mch_id' => config('wechat.payment.default.mch_id'),
            'out_trade_no' => $order_sn,
            'order_id' => $id
        ]);

        $app = $this->payment();
        $jssdk = $app->jssdk;
        $total_fee = env('APP_DEBUG') ? 1 : $order_price;

        // 用 easywechat 封装的方法请求微信的统一下单接口
        $result = $app->order->unify([
            'trade_type'       => 'JSAPI', // 原生支付即扫码支付，商户根据微信支付协议格式生成的二维码，用户通过微信“扫一扫”扫描二维码后即进入付款确认界面，输入密码即完成支付。
            'body'             => 'nihao', // 这个就是会展示在用户手机上巨款界面的一句话, 随便写的
            'out_trade_no'     => $order_sn,
            'total_fee'        => $total_fee,
            'spbill_create_ip' => request()->ip(), // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'openid' => $openid
        ]);

        if ($result['return_code'] == 'SUCCESS') {
            if ($result['result_code'] == 'SUCCESS') {
                $prepayId = $result['prepay_id'];
                $config = $jssdk->sdkConfig($prepayId);

                return [
                    'code' => 0,
                    'data' => $config,
                    'msg' => 'success'
                ];
            } else {
                return [
                    'code' => 202,
                    'msg' => $result['err_code_des'],
                ];
            }
        }
    }

    // 接收微信支付状态的通知
    public function notify()
    {

        // get the raw POST data
        $rawData = file_get_contents("php://input");
        info('notify_data:' . $rawData);
        $app = $this->payment();

        // 用 easywechat 封装的方法接收微信的信息, 根据 $message 的内容进行处理, 之后要告知微信服务器处理好了, 否则微信会一直请求这个 url, 发送信息
        $response = $app->handlePaidNotify(function($message, $fail){
            // 首先查看 order 表, 如果 order 表有记录, 表示已经支付过了
            $order = Order::where('order_sn', $message['out_trade_no'])->first();
            if ($order->status != 0) {
                return true; // 如果已经生成订单, 表示已经处理完了, 告诉微信不用再通知了
            }

            // 查看支付日志
            $payLog = PayLog::where('out_trade_no', $message['out_trade_no'])->first();
            if (!$payLog || $payLog->paid_at) { // 如果订单不存在 或者 订单已经支付过了
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            // return_code 表示通信状态，不代表支付状态
            if ($message['return_code'] === 'SUCCESS') {
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                    // 更新支付时间为当前时间
                    $payLog->paid_at = now();

                    // 更新订单
                    Order::where('id', $order->id)->update([
                        'amount' => $message['total_fee'],
                        'pay_log_id' => $payLog->id,
                        'status' => 1,
                        'paid_at' => $payLog->paid_at,
                    ]);

                    // 更新 PayLog, 这里的字段都是根据微信支付结果通知的字段设置的(https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_7&index=8)
                    PayLog::where('out_trade_no', $message['out_trade_no'])->update([
                        'appid' => $message['appid'],
                        'bank_type' => $message['bank_type'],
                        'total_fee' => $message['total_fee'],
                        'trade_type' => $message['trade_type'],
                        'is_subscribe' => $message['is_subscribe'],
                        'mch_id' => $message['mch_id'],
                        'nonce_str' => $message['nonce_str'],
                        'openid' => $message['openid'],
                        'sign' => $message['sign'],
                        'cash_fee' => $message['cash_fee'],
                        'fee_type' => $message['fee_type'],
                        'transaction_id' => $message['transaction_id'],
                        'time_end' => $payLog->paid_at,
                        'result_code' => $message['result_code'],
                        'return_code' => $message['return_code'],
                    ]);
                }
            } else {
                // 如果支付失败, 也更新 PayLog, 跟上面一样, 就是多了 error 信息
                PayLog::where('out_trade_no', $message['out_trade_no'])->update([
                    'appid' => $message['appid'],
                    'bank_type' => $message['bank_type'],
                    'total_fee' => $message['total_fee'],
                    'trade_type' => $message['trade_type'],
                    'is_subscribe' => $message['is_subscribe'],
                    'mch_id' => $message['mch_id'],
                    'nonce_str' => $message['nonce_str'],
                    'openid' => $message['openid'],
                    'sign' => $message['sign'],
                    'cash_fee' => $message['cash_fee'],
                    'fee_type' => $message['fee_type'],
                    'transaction_id' => $message['transaction_id'],
                    'time_end' => $payLog->paid_at,
                    'result_code' => $message['result_code'],
                    'return_code' => $message['return_code'],
                    'err_code' => $message['err_code'],
                    'err_code_des' => $message['err_code_des'],
                ]);
                return $fail('通信失败，请稍后再通知我');
            }
            return true; // 返回处理完成
        });
        // 这里是必须这样返回的, 会发送给微信服务器处理结果
        return $response;
    }

    public function paid(Request $request)
    {
        $out_trade_no = $request->get('out_trade_no');

        $app = $this->payment();
        // 用 easywechat 封装的方法请求微信
        $result = $app->order->queryByOutTradeNumber($out_trade_no);

        if ($result['trade_state'] === 'SUCCESS') {
            return [
                'code' => 200,
                'msg' => 'paid'
            ];
        }else{
            return [
                'code' => 202,
                'msg' => 'not paid'
            ];
        }
    }
}
