<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolutionOfTheIncidentFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solution_of_the_incident_forms', function (Blueprint $table) {
            $table->id();
            $table->string('proposed_solution');
            $table->string('monitoring_manager');
            $table->string('replacement_out_of_time');
            $table->dateTime('incident_delivery_date');
            $table->integer('days_of_incident_processing');
            $table->decimal('odc_mat_clean', 16,2);
            $table->decimal('cu_prod_clean', 16,2);
            $table->decimal('final_cost_of_clean_material', 16,2);
            $table->decimal('odc_impression', 16,2);
            $table->decimal('printing_cost_per_piece', 16,2);
            $table->decimal('cu_prod_impression', 16,2);
            $table->decimal('total_cost', 16,2);
            $table->foreignId('id_quality_incidents')->references('id')->on('quality_incidents_forms')->onDelete('cascade');
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
        Schema::dropIfExists('solution_of_the_incident_forms');
    }
}
