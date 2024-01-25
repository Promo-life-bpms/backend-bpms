<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDeliveryRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_delivery_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('code_order_route_id')->reference('id')->on('code_order_delivery_routes');
            $table->string("odoo_product_id", 20);
            $table->string('amount');
            $table->string('action');
            $table->dateTime('hour')->nullable();
            $table->text('observations')->nullable();
            $table->string('provider');
            $table->string('origin_address');
            $table->string('destinity_address');
            $table->string('confirmation_sheet')->nullable();
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
        Schema::dropIfExists('product_delivery_routes');
    }
}
