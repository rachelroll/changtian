<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }
}





