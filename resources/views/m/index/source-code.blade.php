<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.bootcss.com/font-awesome/5.8.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/twitter-bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <title>溯源查询</title>
</head>
<body>
<div class="alert alert-success" role="alert">
    <div class="row">
        <h5 class="alert-heading d-block mx-auto">溯源码编号:NO.{{ $source_code }}</h5>
    </div>

</div>
<div class="row">
    <div class="d-block mx-auto">
        <div class="text-info m-2">查询记录</div>
    </div>
</div>
<br>
<div class="container">
    <div class="row">

        <div class="d-block mx-auto">
            @foreach($source_code_model as $source_model)
            <img width="40" src="{{ $source_model->avatar }}" class="rounded-circle" alt=""/>
            @endforeach
        </div>

    </div>
</div>

<div class="container">
    <hr>
    <div class="row">
        <div class="d-block mx-auto">
            <div class="text-info m-2">产品信息</div>
        </div>
    </div>
    <div class="row">
        <ul class=" list-group-flush">
            <li class="list-group-item">净重: {{ $order_item->net_weight }} </li>
            <li class="list-group-item">特色: {{ $order_item->feature }} </li>
            <li class="list-group-item">保鲜期: {{ $order_item->fresh_time }} </li>
            <li class="list-group-item">溯源地: {{ $order_item->source_location }} </li>
            <li class="list-group-item">溯源人: {{ $order_item->source_person }} </li>
        </ul>
    </div>
</div>

<div class="container">
    <hr>
    <div class="row">
        <div class="d-block mx-auto">
            <div class="text-info m-2">追溯信息</div>
        </div>
    </div>
    <div class="row p-3">
        <div class="col">
            <i class="far fa-clock pl-3 text-info" style="font-size: 32px;"></i>
            <div >生产时间:</div>
            <div >{{ date('Y-m-d',strtotime($order_item->created_at ))}}</div>
        </div>
        <div class="col">
            <i class="fa fa-clock pl-3 text-info" style="font-size: 32px;"></i>
            <div>入库时间:</div>
            <div>{{ date('Y-m-d',strtotime($order_item->product_at ))}}</div>
        </div>
        <div class="col">
            <i class="fas fa-hourglass-start pl-3 text-info" style="font-size: 32px;"></i>
            <div>下单时间:</div>
            <div>{{ date('Y-m-d',strtotime($order_item->storage_at ))}}</div>
        </div>
    </div>
    <br>
    <div class="row p-3">
        <div class="col">
            <i class="fas fa-outdent pl-3 text-info" style="font-size: 32px;"></i>
            <div>出库时间:</div>
            <div>{{ date('Y-m-d',strtotime($order_item->out_storage_at ))}}</div>
        </div>
        <div class="col">
            <i class="fas fa-truck pl-3 text-info" style="font-size: 32px;"></i>
            <div>发货时间:</div>
            <div>{{ date('Y-m-d',strtotime($order_item->delivery_at ))}}</div>
        </div>
        <div class="col">
            <i class="fab fa-get-pocket pl-3 text-info" style="font-size: 32px;"></i>
            <div>收货时间:</div>
            <div>{{ date('Y-m-d',strtotime($order_item->recept_at ))}}</div>
        </div>
    </div>
</div>

{{--企业信息--}}
<div class="container">
    <hr>
    <div class="row">
        <div class="d-block mx-auto">
            <div class="text-info m-2">企业信息</div>
        </div>
    </div>
    <div class="row p-3">
        <div class="col">
            <div >供应商: 宁夏昌田农业发展有限公司</div>
            <div >热线: 400-666-8683</div>
            <div >企业地址: 宁夏固原市</div>
        </div>
    </div>
    <br>
    <br>
</div>

</body>
</html>
