<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Good extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'code' => 0,
            'data' => [
                'basicInfo' => [
                    'id' => $this->id,
                    'name' => $this->name, // 商品名称
                    'minprice' => $this->price,
                    'pic' => $this->pictures, //封面图
                    'shipping_date' => $this->shipping_date,
                    'shipping_place' => $this->shipping_place,
                    'content' => $this->intro, // 商品简介
                    'stores' => 100
                ],
                'category' => [
                    'name' => '水果'
                ],
            ],
            'msg' => 'success'
        ];
    }
}
