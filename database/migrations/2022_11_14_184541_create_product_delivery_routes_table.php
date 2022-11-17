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
            $table->foreignId('delivery_id')->reference('id')->on('delivery_routes');
            $table->string('code_sale');
            $table->string('code_order');
            $table->string('type_of_origin');
            $table->text('delivery_address');
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
        Schema::dropIfExists('product_delivery_routes');
    }
}
