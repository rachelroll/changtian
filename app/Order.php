<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $guarded = [];

    //订单状态
    public const STATUS_WAIT_PAY = 0;  //待支付
    public const STATUS_PAID = 1;  //已支付
    public const STATUS_CONFIRM = 2;  //已发货
    public const STATUS_SHIPPED = 3;  //已完成
    public const STATUS_RECEIVED = 4;  //已确认收货
    public const STATUS_FAILD = 98;  //支付失败
    public const STATUS_CANCEL = -1;  //已取消

    public const STATUS = ['待支付', '待发货', '待收货', '已完成'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}










