<?php

namespace App\Http\Controllers\api;

use App\Good;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Good as GoodResource;

class GoodsController extends Controller
{
    // 商品详情
    public function show(Request $request)
    {
        $id = $request->id;

        $good = Good::find($id);

        $arr = [];

        foreach ($good->pictures as $item) {
            $arr[] = config('filesystems.disks.oss.cdnDomain') . '/' . $item;
        }

        $good->pics = $arr;
        return new GoodResource($good);
    }

    // 商品列表
    public function index(Request $request)
    {
        if($request->id) {
            $goods = Good::where('category_id', $request->id)->get();
            return GoodResource::collection($goods);
        }

        return GoodResource::collection(Good::all());
    }
}
