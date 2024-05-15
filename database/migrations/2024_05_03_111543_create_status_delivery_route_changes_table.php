<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusDeliveryRouteChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_delivery_route_changes', function (Blueprint $table) {
            $table->id();
            $table->integer('order_purchase_product_id');
            $table->string('code_order');
            $table->string('status');
            $table->integer('visible')->nullable();
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
        Schema::dropIfExists('status_delivery_route_changes');
    }
}
