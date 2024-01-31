<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangeReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_returns', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_return', 16,2);
            $table->string('status')->default('Sin confirmar');
            $table->dateTime('confirmation_datetime')->nullable();
            $table->integer('confirmation_user_id')->nullable();
            $table->string('description'); 
            $table->string('file_exchange_returns')->nullable();
            $table->foreignId('return_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('purchase_id')->references('id')->on('purchase_requests')->onDelete('cascade');
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
        Schema::dropIfExists('exchange_returns');
    }
}
