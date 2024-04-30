<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_confirmations', function (Blueprint $table) {
            $table->id();
            $table->integer('status');
            $table->string('description');
            $table->string('code_sale');
            $table->foreignId('order_purchase_id')->references('id')->on('order_purchases')->onDelete('cascade');
            $table->foreignId('id_order_products')->references('id')->on('order_purchase_products')->onDelete('cascade');
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
        Schema::dropIfExists('order_confirmations');
    }
}
