<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];



    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getSourceAttributeAttribute($value)
    {
        return explode("\r\n", $value);
    }
}
