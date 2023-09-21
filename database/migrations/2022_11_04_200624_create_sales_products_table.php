<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained();
            $table->string("odoo_product_id", 20);
            $table->text("product");
            $table->text("description");
            $table->string("customization");
            $table->string("provider");
            $table->string("logo");
            $table->string("key_product");
            $table->string("type_sale");
            $table->decimal("cost_labeling", 8, 2);
            $table->decimal("clean_product_cost", 8, 2);
            $table->integer("quantity_ordered");
            $table->integer("quantity_delivered");
            $table->integer("quantity_invoiced");
            $table->decimal("unit_price", 8, 2);
            $table->decimal("subtotal", 8, 2);
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
        Schema::dropIfExists('sales_products');
    }
}
