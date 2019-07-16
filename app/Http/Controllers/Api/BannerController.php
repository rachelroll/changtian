<?php

namespace App\Http\Controllers\api;

use App\Banner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Banner as BannerResource;

class BannerController extends Controller
{

    public function index()
    {
        return BannerResource::collection(Banner::where('enabled',1)->get());
    }
}
