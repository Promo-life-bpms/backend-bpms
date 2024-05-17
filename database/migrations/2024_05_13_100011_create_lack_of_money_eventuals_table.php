<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLackOfMoneyEventualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lack_of_money_eventuals', function (Blueprint $table) {
            $table->id();
            $table->integer('id_applicant_person');
            $table->integer('id_person_who_delivers')->nullable();
            $table->text('description');
            $table->string('file');
            $table->decimal('previous_total', 16,2);
            $table->decimal('current_total', 16,2);
            $table->string('status');
            $table->dateTime('confirmation_datetime')->nullable();
            $table->foreignId('id_eventual')->references('id')->on('eventuales')->onDelete('cascade');
            $table->foreignId('id_purchase')->references('id')->on('purchase_requests')->onDelete('cascade');
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
        Schema::dropIfExists('lack_of_money_eventuals');
    }
}
