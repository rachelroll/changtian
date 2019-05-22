<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('order_id')->default(0)->comment('订单编号');
            $table->unsignedInteger('good_id')->default(0)->comment('商品ID');
            $table->string('name')->default('')->comment('商品名称');
            $table->string('price')->default('')->comment('商品单价');
            $table->unsignedInteger('quantity')->default(0)->comment('购买数量');

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
        Schema::dropIfExists('order_items');
    }
}
