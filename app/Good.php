<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Good extends Model
{

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getPicturesAttribute($pictures)
    {
        return json_decode($pictures, true) ?: [];
    }

    public function video()
    {
        return $this->BelongsTo(Video::class,'videoId','id');
    }
}
