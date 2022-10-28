<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Orden de Venta o Pedido de Venta Confirmado
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string("order_code", 25);
            $table->string("client", 200);
            $table->text("invoice_address",);
            $table->text("delivery_address");
            $table->dateTime("delivery_time");
            $table->dateTime("order_date");
            $table->dateTime("confirmation_date");
            $table->text("delivery_instruction");
            $table->string("company", 30);
            $table->dateTime('planned_date');
            $table->dateTime('commitment_date');
            $table->string('seller_name', 200);
            $table->integer('seller_id');
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
        Schema::dropIfExists('sales_orders');
    }
}
