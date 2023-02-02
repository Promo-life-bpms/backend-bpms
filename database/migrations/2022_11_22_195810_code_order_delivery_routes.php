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
            $table->string('code_sale');
            $table->string('code_order');
            $table->string('type_of_origin');
            $table->text('origin_address');
            $table->string('type_of_destiny');
            $table->text('destiny_address');
            $table->time('hour');
            $table->string('attention_to');
            $table->string('action');
            $table->string('num_guide')->nullable();
            $table->text('observations')->nullable();
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
