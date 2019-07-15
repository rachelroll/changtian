<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    const STATUS = ['待支付', '待发货', '待收货', '已完成'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}










