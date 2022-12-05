<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId("inspection_id")->references('id')->on('inspections');
            $table->foreignId("product_id")->references('id')->on('order_purchase_products');
            $table->foreignId("order_purchase_id")->references('id')->on('order_purchases');
            $table->string("quantity_selected");
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
        Schema::dropIfExists('inspection_products');
    }
}
