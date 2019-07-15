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
        if($request->id) {
            $goods = Good::where('category_id', $request->id)->get();
        } else {
            $goods = Good::all();
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
            $videoMp4Src = config('filesystems.disks.oss.cdnDomain') . '/' .$video->fdMp4;
        } else {
            $videoMp4Src = null;
        }

        return [
            'code' => 0,
            'data' => $videoMp4Src,
            'msg'  => 'success',
        ];
    }
}
