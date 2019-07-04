<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paylog extends Model
{

    protected $guarded = [];

    public function good()
    {
        return $this->belongsTo(Good::class);
    }
}
