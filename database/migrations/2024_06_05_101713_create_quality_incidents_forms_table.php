<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQualityIncidentsFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quality_incidents_forms', function (Blueprint $table) {
            $table->id();
            $table->string('code_sale')->nullable();
            $table->string('incidence_folio')->nullable();
            $table->integer('days_in_warehouse')->nullable();
            $table->dateTime('incident_date')->nullable();
            $table->foreignId('id_sale_product')->references('id')->on('sales_products')->onDelete('cascade');
            $table->integer('sale_product_quantity')->nullable();
            $table->string('logo')->nullable();
            $table->foreignId('id_order_products')->references('id')->on('order_purchase_products')->onDelete('cascade');
            $table->integer('order_product_quantity')->nullable();
            $table->string('maquilador')->nullable();
            $table->string('distributor')->nullable();
            $table->integer('correct_parts')->nullable();
            $table->integer('defective_parts')->nullable();
            $table->integer('defect_percentage')->nullable();
            $table->string('responsible')->nullable();
            $table->string('general_cause')->nullable();
            $table->string('return_description')->nullable();
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
        Schema::dropIfExists('quality_incidents_forms');
    }
}
