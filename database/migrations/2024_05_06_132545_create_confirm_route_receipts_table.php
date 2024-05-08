<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfirmRouteReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('confirm_route_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_order_purchase_products')->references('id')->on('order_purchase_products')->onDelete('cascade');
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
        Schema::dropIfExists('confirm_route_receipts');
    }
}
