<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_lists', function (Blueprint $table) {
            $table->id();
            $table->text('code_sale');
            $table->text('order_com')->nullable();
            $table->text('virtual')->nullable();
            $table->text('arte')->nullable();
            $table->text('logo')->nullable();
            $table->text('contact')->nullable();
            $table->text('quote_pro')->nullable();
            $table->text('distribution')->nullable();
            $table->text('delivery_address')->nullable();
            $table->text('data_invoicing')->nullable();
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
        Schema::dropIfExists('check_lists');
    }
}
