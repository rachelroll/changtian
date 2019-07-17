<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Banner extends JsonResource
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
            'picUrl' => config('filesystems.disks.oss.cdnDomain') . '/' . $this->picUrl,
            'businessId' => $this->id,
            'slogan' => $this->slogan,
        ];
    }
}
