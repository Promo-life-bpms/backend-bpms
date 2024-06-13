<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleStatusChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_status_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId("sale_id")->constrained();
            $table->foreignId("status_id")->constrained();
            $table->integer('status');
            $table->integer('visible')->nullable();
            $table->string('status_name')->nullable();
            $table->string('slug')->nullable();
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
        Schema::dropIfExists('sale_status_changes');
    }
}
