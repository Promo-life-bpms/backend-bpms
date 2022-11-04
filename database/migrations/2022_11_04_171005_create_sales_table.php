<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('code_sale', 20);
            $table->string('name_sale', 255);
            $table->string('directed_to', 255)->nullable();
            $table->string('invoice_address', 255);
            $table->string('delivery_address', 255);
            $table->string('delivery_instructions', 255);
            $table->string('delivery_time', 255);
            $table->string('confirmation_date', 255);
            $table->string('additional_information', 255);
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
        Schema::dropIfExists('sales');
    }
}
