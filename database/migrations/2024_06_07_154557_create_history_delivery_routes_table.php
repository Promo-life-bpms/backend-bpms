<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryDeliveryRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_delivery_routes', function (Blueprint $table) {
            $table->id();
            $table->string('code_sale');
            $table->string('code_order');
            $table->integer('product_id');
            $table->string('type');
            $table->string('type_of_destiny');
            $table->date('date_of_delivery');
            $table->string('status_delivery');
            $table->string('shipping_type');
            $table->integer('color');
            $table->integer('visible');
            $table->string('observation');
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
        Schema::dropIfExists('history_delivery_routes');
    }
}
