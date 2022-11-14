<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('code_order', 20);
            $table->string('code_sale', 20);
            $table->string('provider_name');
            $table->string('sequence', 20);
            $table->dateTime('order_date');
            $table->dateTime('planned_date');
            $table->text('deliver_in');
            $table->string('company');
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
        Schema::dropIfExists('order_purchases');
    }
}
