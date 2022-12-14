<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusOTTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_o_t', function (Blueprint $table) {
            $table->id();
            $table->time('hora');
            $table->foreignId('id_order_purchase')
            ->nullable()
            ->constrained('order_purchases')
            ->cascadeOnUpdate()
            ->nullOnDelete();
            $table->string('status');
            $table->foreignId('id_order_purchase_products')
            ->nullable()
            ->constrained('order_purchase_products')
            ->cascadeOnUpdate()
            ->nullOnDelete();
            $table->string('cantida_seleccionada');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_o_t');
    }
}
