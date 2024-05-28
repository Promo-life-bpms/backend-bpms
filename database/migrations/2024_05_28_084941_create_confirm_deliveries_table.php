<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfirmDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('confirm_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_order_purchase_product')->references('id')->on('order_purchase_products')->onDelete('cascade');
            $table->string('delivery_type');
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
        Schema::dropIfExists('confirm_deliveries');
    }
}
