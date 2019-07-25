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
                    'kind' => $this->kind, // 品种
                    'intro' => $this->intro, // 简介
                    'minprice' => $this->price,
                    'cover' => $this->cover, //封面图
                    'pic' => $this->pics,
                    'shipping_date' => $this->shipping_date,
                    'shipping_place' => $this->shipping_place,
                    'content' => $this->intro, // 商品简介
                    'stores' => 100,
                    'size' => $this->size, // 规格与包装
                    'is_source' => $this->is_source, // 是否溯源
                    'source_content' => $this->source_content, // 是否溯源
                    'source_video' => $this->source_video ? config('filesystems.disks.oss.cdnDomain') . '/' . $this->source_video : null, // 是否溯源
                    'videoId' => $this->video->fbMp4 ?? $this->videoId, // 视频 ID
                ],
                'category' => [
                    'name' => $this->category->name
                ],
            ],
            'msg' => 'success'
        ];
    }
}
