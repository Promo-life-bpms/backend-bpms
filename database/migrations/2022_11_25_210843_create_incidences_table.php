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
            $table->string('area')->nullable();
            $table->text('reason')->nullable();
            $table->string('product_type')->nullable();
            $table->text('evidence')->nullable();
            $table->string('solution')->nullable();
            $table->string('responsible')->nullable();
            $table->date('solution_date')->nullable();
            $table->string("comments")->nullable();
            $table->string('elaborated')->nullable();
            $table->text('signature_elaborated')->nullable();
            $table->string('reviewed')->nullable();
            $table->text('signature_reviewed')->nullable();
            $table->text("description");
            $table->string('type_of_technique')->nullable();
            $table->string('user_solution')->nullable();
            $table->date('creation_date')->nullable();
            $table->text('status');
            //Creacion de llave foranea
            $table->foreignId('sale_id')
                ->nullable()
                ->constrained('sales')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->timestamps();
            /*
            $table->string("company");
            $table->string("odoo_status");
            $table->boolean("sync_with_odoo")->default(true);
            $table->string('internal_code_incidence')->nullable();
            $table->string('rol_creator')->nullable();

            $table->string('bpm_status')->nullable();
            $table->date('commitment_date')->nullable();
            $table->string('user_id')->nullable();
            //Creacion de llave foranea
            $table->foreignId('sale_id')
                ->nullable()
                ->constrained('sales')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->timestamps(); */
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
