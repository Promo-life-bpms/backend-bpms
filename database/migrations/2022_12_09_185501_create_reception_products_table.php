<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceptionProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reception_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reception_id')->constrained();
            $table->string('code_reception');
            $table->string('odoo_product_id');
            $table->string('product');
            $table->integer('initial_demand');
            $table->integer('done');
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
        Schema::dropIfExists('reception_products');
    }
}
