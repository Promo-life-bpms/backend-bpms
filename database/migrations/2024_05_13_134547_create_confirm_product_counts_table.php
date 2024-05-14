<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfirmProductCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('confirm_product_counts', function (Blueprint $table) {
            $table->id();
            $table->string('id_product');
            $table->string('type');
            $table->string('confirmation_type');
            $table->text('observation')->nullable();
            $table->foreignId('id_confirm_routes')->references('id')->on('confirm_routes')->onDelete('cascade');
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
        Schema::dropIfExists('confirm_product_counts');
    }
}
