<?php

namespace App\Http\Controllers\api;

use App\Address;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Address as AddressResource;

class AddressController extends Controller
{
    // 地址列表
    public function index()
    {
        $addresses = Address::all();
        if ($addresses) {
            return AddressResource::collection($addresses);
        } else{
            return [
                'code' => 700,
                'msg' => '没有地址列表'
            ];
        }
    }

    // 新增收货地址
    public function add(Request $request)
    {
        $provinceId = $request->provinceId;
        $cityId = $request->cityId;
        $districtId = $request->districtId;
        $address = $request->address;
        $contact_name = $request->linkMan;
        $phone = $request->mobile;
        $code = $request->code;
        $isDefault = $request->isDefalut;

        $bool = Address::create([
            'provinceId' => $provinceId,
            'cityId' => $cityId,
            'districtId' => $districtId,
            'address' => $address,
            'contact_name' => $contact_name,
            'phone' => $phone,
            'code' => $code,
            'isDefault' => $isDefault
        ]);

        if ($bool) {
            return [
                'code' => 0,
                'msg' => 'success'
            ];
        } else {
            return [
                'code' => 202,
                'msg' => 'fail'
            ];
        }
    }

    // 更新收货地址
    public function update(Request $request)
    {
        $id = $request->id;
        $provinceId = $request->provinceId;
        $cityId = $request->cityId;
        $districtId = $request->districtId;
        $address = $request->address;
        $contact_name = $request->linkMan;
        $phone = $request->mobile;
        $code = $request->code;

        $bool = Address::where('id', $id)
            ->update([
            'provinceId' => $provinceId,
            'cityId' => $cityId,
            'districtId' => $districtId,
            'address' => $address,
            'contact_name' => $contact_name,
            'phone' => $phone,
            'code' => $code,
            'isDefault' => ''
        ]);

        if ($bool) {
            return [
                'code' => 0,
                'msg' => 'success'
            ];
        } else {
            return [
                'code' => 202,
                'msg' => 'fail'
            ];
        }
    }

    // 获取默认收货地址
    public function default()
    {
        $address = Address::where('isDefault', 'true')->first();

        if ($address) {
            return new AddressResource($address);
        } else {
            return [
                'code' =>400,
                'msg' => '尚无默认地址',
            ];
        }
    }

    // 地址详情
    public function detail(Request $request)
    {
        $id = $request->id;
        $address = Address::where('id', $id)->first();

        return new AddressResource($address);
    }
}
