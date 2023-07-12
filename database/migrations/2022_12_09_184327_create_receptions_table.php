<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receptions', function (Blueprint $table) {
            $table->id();
            $table->string('code_reception');
            $table->string('code_order');
            $table->string('company');
            $table->string('type_operation');
            $table->dateTime('planned_date')->nullable();
            $table->dateTime('effective_date')->nullable();
            $table->string('status');
            $table->integer('user_id')->nullable();
            $table->boolean('maquilador');
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
        Schema::dropIfExists('receptions');
    }
}
