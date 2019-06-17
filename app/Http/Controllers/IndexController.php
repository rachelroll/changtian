<?php

namespace App\Http\Controllers;

use App\OrderItem;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class IndexController extends Controller
{

    public function sourceCode($id)
    {
        $source_code = $id;
        $hashids = new Hashids('ross',4,'abcdefghijklmnopqrstuvwxyz');
        $id = $hashids->decode($id);
        if ($id) {
            $id = Arr::first($id);
            $order_item = OrderItem::find($id);
            if ($order_item) {
                return view('m.index.source-code',compact('order_item','source_code'));
            }
            echo '错误参数';
            die;

        } else {
            echo '参数错误';
            die;
        }

    }
}
