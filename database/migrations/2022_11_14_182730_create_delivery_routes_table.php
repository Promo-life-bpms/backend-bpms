<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDeliveryRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('code_sale')->reference('code_sale')->on('sales');
            $table->foreignId('code_order')->reference('code_order')->on('code_order');
            $table->date('product_id');
            $table->string('type_of_destiny');
            $table->string('date_of_delivery');
            $table->string('status_delivery');
            $table->string('shipping_type');
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
        Schema::dropIfExists('delivery_routes');
    }
}
