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
                    'pic' => "https://cdn.it120.cc/apifactory/2019/05/11/46e2ebcb-a9dd-4e83-b020-0076eedf006a.JPG", //封面图
                    'shipping_date' => $this->shipping_date,
                    'shipping_place' => $this->shipping_place
                ],
                'category' => [
                    'name' => '水果'
                ],
                'content' => $this->intro, // 商品简介
            ],
            'msg' => 'success'
        ];
    }
}
