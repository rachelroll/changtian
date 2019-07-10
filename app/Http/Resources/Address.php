<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Address extends JsonResource
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
                'linkMan' => $this->contact_name,
                'mobile' => $this->phone,
                'address' => $this->address,
                'id' => $this->id,
                'provinceId' => $this->provinceId,
                'cityId' => $this->cityId,
                'districtId' => $this->districtId,
                'code' => $this->code,
                'provinceStr' => $this->provinceStr,
                'areaStr' => $this->areaStr,
                'cityStr' => $this->cityStr,
            ],
            'msg' => 'success'
        ];
    }
}
