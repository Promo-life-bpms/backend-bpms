<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfirmRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('confirm_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_product_order')->references('id')->on('order_purchase_products')->onDeete('cascade');
            $table->foreignId('id_delivery_routes')->references('id')->on('delivery_routes')->onDelete('cascade');
            $table->text('reception_type');
            $table->text('destination');
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
        Schema::dropIfExists('confirm_routes');
    }
}
