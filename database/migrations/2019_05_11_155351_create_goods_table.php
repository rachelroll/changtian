<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->default('')->comment('商品名称');
            $table->string('intro')->default('')->comment('商品简介');
            $table->string('kind')->default('')->comment('品种');
            $table->string('shipping_date')->default('')->comment('发货期限');
            $table->string('shipping_place')->default('')->comment('发货地');
            $table->decimal('price')->default(0)->comment('单价');
            $table->string('pictures')->default('')->comment('商品图片');
            $table->string('category_id')->default(0)->comment('分类 ID');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods');
    }
}
