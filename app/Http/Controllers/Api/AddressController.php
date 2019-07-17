<?php

namespace App\Http\Controllers\api;

use App\Address;
use App\ChinaArea;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Address as AddressResource;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{

    // 地址列表
    public function index()
    {
        $addresses = Address::where('user_id',request()->user()->id)->get();
        if ($addresses) {
            return AddressResource::collection($addresses);
        } else {
            return [
                'code' => 700,
                'msg'  => '没有地址列表',
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
        $isDefault = $request->isDefault;
        $provinceStr = ChinaArea::where('code', substr($provinceId,0,6))->first()->name;
        $areaStr = ChinaArea::where('code', substr($cityId,0,6))->first()->name;
        $cityStr = ChinaArea::where('code', substr($districtId,0,6))->first()->name;

        $bool = Address::create([
            'provinceId'   => $provinceId,
            'cityId'       => $cityId,
            'districtId'   => $districtId,
            'address'      => $address,
            'contact_name' => $contact_name,
            'phone'        => $phone,
            'code'         => $code,
            'isDefault'    => $isDefault,
            'provinceStr'  => $provinceStr,
            'areaStr'      => $areaStr,
            'cityStr'      => $cityStr,
            'user_id'      => $request->user()->id,
        ]);

        if ($bool) {
            return [
                'code' => 0,
                'msg'  => 'success',
            ];
        } else {
            return [
                'code' => 202,
                'msg'  => 'fail',
            ];
        }
    }

    // 更新收货地址
    public function update(Request $request)
    {
        $id = $request->id;
        $isDefault = $request->isDefault;

        if ($isDefault == '1') {
            Address::where('isDefault', '1')->update([
                'isDefault' => '0'
            ]);
            $bool = Address::where('id', $id)->update([
                'isDefault' => $isDefault,
            ]);
        } elseif($isDefault == '0') {
            $provinceId = $request->provinceId;
            $cityId = $request->cityId;
            $districtId = $request->districtId;
            $address = $request->address;
            $contact_name = $request->linkMan;
            $phone = $request->mobile;
            $code = $request->code;

            $provinceStr = ChinaArea::where('code', substr($provinceId,0,6))->first()->name;
            $areaStr = ChinaArea::where('code', substr($cityId,0,6))->first()->name;
            $cityStr = ChinaArea::where('code', substr($districtId,0,6))->first()->name;

            $bool = Address::where('id', $id)->update([
                'provinceId'   => $provinceId,
                'cityId'       => $cityId,
                'districtId'   => $districtId,
                'address'      => $address,
                'contact_name' => $contact_name,
                'phone'        => $phone,
                'code'         => $code,
                'provinceStr'  => $provinceStr,
                'areaStr'      => $areaStr,
                'cityStr'      => $cityStr,
                'isDefault' => $isDefault,
            ]);
        }



        if ($bool) {
            return [
                'code' => 0,
                'msg'  => 'success',
            ];
        } else {
            return [
                'code' => 202,
                'msg'  => 'fail',
            ];
        }
    }

    // 获取默认收货地址
    public function default()
    {
        $address = Address::where('isDefault', 1)->where('user_id',request()->user()->id)->first();

        if ($address) {
            return new AddressResource($address);
        } else {
            return [
                'code' => 400,
                'msg'  => '尚无默认地址',
            ];
        }
    }

    // 地址详情
    public function detail(Request $request)
    {

        $id = $request->id;
        $address = Address::where('id', $id)->where('user_id',$request->user()->id)->first();

        return new AddressResource($address);
    }

    // 地址详情
    public function delete(Request $request)
    {

        $id = $request->id;
        $res = Address::where('id', $id)->where('user_id',$request->user()->id)->delete();

        return response()->json([
            'code'=>200,
            'msg'=>'删除成功',
        ]);

    }
}
