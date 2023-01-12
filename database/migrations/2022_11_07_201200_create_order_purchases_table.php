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
            $table->text('provider_name');
            $table->text('provider_address');
            $table->text('supplier_representative');
            $table->string('sequence', 20);
            $table->dateTime('order_date')->nullable();
            $table->dateTime('planned_date')->nullable();
            $table->string('company');
            $table->string("status");
            $table->string("status_bpm");
            $table->string("type_purchase");
            $table->decimal("total", 10, 2);
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
