<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPurchaseProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_purchase_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_purchase_id')->constrained();
            $table->string("odoo_product_id", 20);
            $table->string("product");
            $table->text("description");
            $table->dateTime("planned_date")->nullable();
            $table->string("company");
            $table->integer("quantity");
            $table->integer("quantity_invoiced");
            $table->integer("quantity_delivered");
            $table->decimal("unit_price", 10, 2);
            $table->decimal("subtotal", 10, 2);
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
        Schema::dropIfExists('order_purchase_products');
    }
}
