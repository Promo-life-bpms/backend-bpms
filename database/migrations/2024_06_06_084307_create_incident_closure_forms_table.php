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
            $table->string('status');
            $table->string('application');
            $table->string('note_of_application');
            $table->string('responsible_for_final_monitoring');
            $table->string('final_status');
            $table->dateTime('final_closing_date');
            $table->string('credit_note');
            $table->integer('days_of_incident_process');
            $table->unsignedBigInteger('id_solution_incident');
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
