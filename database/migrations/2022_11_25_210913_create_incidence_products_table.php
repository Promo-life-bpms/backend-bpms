<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidenceProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidence_products', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity_selected');
            $table->string('request')->nullable();
            $table->text('notes')->nullable();
            $table->text('product')->nullable();
            $table->double('cost', 10, 2)->nullable();
            $table->foreignId('order_purchase_product_id')
                ->nullable()
                ->constrained('order_purchase_products')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('incidence_id')
                ->nullable()
                ->constrained('incidences')
                ->cascadeOnUpdate()
                ->nullOnDelete();
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
        Schema::dropIfExists('incidence_products');
    }
}
