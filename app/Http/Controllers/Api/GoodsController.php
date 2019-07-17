<?php

namespace App\Http\Controllers\api;

use App\Good;
use App\Video;
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
        $goodsModel = Good::where('enabled',1);
        if($request->id) {
            $goods = $goodsModel->where('category_id', $request->id)->get();
        } elseif($request->nameLike) {
            $goods = $goodsModel->where('name', 'like', '%'.$request->nameLike.'%')->get();
        } else {
            $goods = $goodsModel->get();
        }
        foreach ($goods as &$good) {
            $good->cover = config('filesystems.disks.oss.cdnDomain') . '/' . $good->pictures[0];
        }

        return GoodResource::collection($goods);
    }

    // 视频
    public function mediaVideoDetail(Request $request)
    {
        $video_id = $request->videoId;

        $video = Video::find($video_id);

        if ($video) {
            $fdMp4 = config('filesystems.disks.oss.cdnDomain') . '/' .$video->fdMp4;
        } else {
            $fdMp4 = null;
        }

        return [
            'code' => 0,
            'data' => [
                'fdMp4' => $fdMp4
            ],
            'msg'  => 'success',
        ];
    }
}
