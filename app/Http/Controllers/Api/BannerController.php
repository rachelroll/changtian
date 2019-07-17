<?php

namespace App\Http\Controllers\api;

use App\Banner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Banner as BannerResource;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{

    public function index()
    {
        $slogan = DB::table('admin_users')->pluck('slogan')->first();

        $banners = Banner::where('enabled',1)->get();

        foreach ($banners as &$item) {
            $item->picUrl = config('filesystems.disks.oss.cdnDomain') . '/' . $item->picUrl;
        }

        return [
            'code' => 0,
            'data' => $banners,
            'slogan' => $slogan
        ];
    }
}
