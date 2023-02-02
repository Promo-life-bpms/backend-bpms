<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRemissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_remission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remission_id')->reference('id')->on('remisiones');
            $table->integer('delivered_quantity');
            $table->string('order_purchase_product_id');
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
        Schema::dropIfExists('product_remission');
    }
}
