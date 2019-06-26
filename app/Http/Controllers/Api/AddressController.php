<?php

namespace App\Http\Controllers\api;

use App\Address;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Address as AddressResource;

class AddressController extends Controller
{
    // µØÖ·ÁĞ±í
    public function index()
    {
        return AddressResource::collection(Address::all());
    }

    public function add(Request $request)
    {
        $provinceId = $request->provinceId;
        $cityId = $request->cityId;
        $districtId = $request->districtId;
        $address = $request->address;
        $contact_name = $request->contact_name;
        $phone = $request->phone;

        $bool = Address::create([
            'provinceId' => $provinceId,
            'cityId' => $cityId,
            'districtId' => $districtId,
            'address' => $address,
            'contact_name' => $contact_name,
            'phone' => $phone
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
}
