<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CodeOrderDeliveryRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('code_order_delivery_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_route_id')->reference('id')->on('delivery_routes');
            $table->foreignId('user_chofer_id')->reference('id')->on('users')->nullable();
            $table->foreignId('parcel_id')->reference('id')->on('parcels')->nullable();
            $table->string('parcel_name')->nullable();
            $table->string('type_of_chofer')->nullable();
            $table->string('type_of_product')->nullable();
            $table->string('code_sale');
            $table->string('code_order');
            $table->string('type_of_origin');
            $table->string('type_of_destiny');
            $table->string('status')->nullable();
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
        Schema::dropIfExists('code_order_delivery_routes');
    }
}
