<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusProductsOTSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_products_o_t_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_status_o_t_s')
            ->nullable()
            ->constrained('status_o_t_s')
            ->cascadeOnUpdate()
            ->nullOnDelete();
            $table->foreignId('id_order_purchase_products')
            ->nullable()
            ->constrained('order_purchase_products')
            ->cascadeOnUpdate()
            ->nullOnDelete();
            $table->string('cantidad_seleccionada');
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
        Schema::dropIfExists('status_products_o_t_s');
    }
}
