<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItem extends JsonResource
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
                'name' => $this->name,
                'price' => $this->price,
                'quantity' => $this->quantity,
                'cover' => $this->cover
            ]
        ];
    }
}
