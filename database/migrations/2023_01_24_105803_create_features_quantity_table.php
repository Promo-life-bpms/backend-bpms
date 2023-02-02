<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeaturesQuantityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('features_quantity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained();
            $table->integer('wrong_pantone_color');
            $table->integer('damage_logo');
            $table->integer('incorrect_logo');
            $table->integer('incomplete_pieces');
            $table->integer('merchandise_not_cut');
            $table->integer('different_dimensions');
            $table->integer('damaged_products');
            $table->integer('product_does_not_perform_its_function');
            $table->integer('wrong_product_code');
            $table->integer('total');
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
        Schema::dropIfExists('features_quantity');
    }
}
