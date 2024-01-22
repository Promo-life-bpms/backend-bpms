<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundOfMoneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_of_money', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_returned', 16,2);
            $table->decimal('total_spent', 16,2);
            $table->string('period');
            $table->string('was_returned_to');
            $table->foreignId('id_user')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('refund_of_money');
    }
}
