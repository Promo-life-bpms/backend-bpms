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
            $table->text('invoice_address');
            $table->text('delivery_address');
            $table->text('delivery_instructions');
            $table->dateTime('delivery_time');
            $table->dateTime('confirmation_date');
            $table->dateTime('order_date');
            $table->text('additional_information');
            $table->string('commercial_name');
            $table->string('commercial_email');
            $table->string('commercial_odoo_id');
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
