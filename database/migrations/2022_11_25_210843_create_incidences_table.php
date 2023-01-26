<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidences', function (Blueprint $table) {
            $table->id();
            $table->string("code_incidence");
            $table->string("code_sale");
            $table->string("client");
            $table->string("requested_by");
            $table->text("description");
            $table->date("date_request")->nullable();
            $table->string("company");
            $table->string("odoo_status");

            $table->string('internal_code_incidence')->nullable();
            $table->string('area')->nullable();
            $table->string('reason')->nullable();
            $table->string('product_type')->nullable();
            $table->string('type_of_technique')->nullable();
            $table->string('responsible')->nullable();
            $table->date('creation_date')->nullable();
            $table->string('bpm_status')->nullable();
            $table->text('evidence')->nullable();
            $table->date('commitment_date')->nullable();
            $table->string('solution')->nullable();
            $table->date('solution_date')->nullable();
            $table->string('user_id')->nullable();
            $table->string('elaborated')->nullable();
            $table->text('signature_elaborated')->nullable();
            $table->string('reviewed')->nullable();
            $table->text('signature_reviewed')->nullable();
            //Creacion de llave foranea
            $table->foreignId('sale_id')
                ->nullable()
                ->constrained('sales')
                ->cascadeOnUpdate()
                ->nullOnDelete();
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
        Schema::dropIfExists('incidences');
    }
}
