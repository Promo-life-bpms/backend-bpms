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
            $table->string('code_sale');
            $table->string('incidence_folio');
            $table->integer('days_in_warehouse');
            $table->dateTime('incident_date');
            $table->foreignId('id_sale_product')->references('id')->on('sales_products')->onDelete('cascade');
            $table->integer('sale_product_quantity');
            $table->string('logo');
            $table->foreignId('id_order_products')->references('id')->on('order_purchase_products')->onDelete('cascade');
            $table->integer('order_product_quantity');
            $table->string('maquilador')->nullable();
            $table->string('distributor')->nullable();
            $table->integer('correct_parts');
            $table->integer('defective_parts');
            $table->integer('defect_percentage');
            $table->string('responsible');
            $table->string('general_cause');
            $table->string('return_description');
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
