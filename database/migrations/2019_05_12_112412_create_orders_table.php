<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->decimal('amount')->default(0)->comment('订单总金额');
            $table->string('username')->default('')->comment('客户名字');
            $table->string('contact')->default('')->comment('客户联系方式');
            $table->string('address')->default('')->comment('邮寄地址');
            $table->string('comments')->default('')->comment('备注');

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
        Schema::dropIfExists('orders');
    }
}
