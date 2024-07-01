<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentClosureFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_closure_forms', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->string('application')->nullable();
            $table->string('note_of_application')->nullable();
            $table->string('responsible_for_final_monitoring')->nullable();
            $table->string('final_status')->nullable();
            $table->dateTime('final_closing_date')->nullable();
            $table->string('credit_note')->nullable();
            $table->integer('days_of_incident_process')->nullable();
            $table->unsignedBigInteger('id_solution_incident')->nullable();
            $table->foreign('id_solution_incident')->references('id')->on('solution_of_the_incident_forms')->onDelete('cascade');
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
        Schema::dropIfExists('incident_closure_forms');
    }
}
