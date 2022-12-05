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
            $table->string('num_incidencia');
            $table->string('area');
            $table->string('motivo');
            $table->string('tipo_de_producto');
            $table->string('tipo_de_tecnica')->nullable();
            $table->string('solucion_de_incidencia');
            $table->string('responsable');
            $table->date('fecha_creacion');
            $table->string('status');
            $table->text('evidencia');
            $table->date('fecha_compromiso');
            $table->string('solucion');
            $table->date('fecha_solucion');
            $table->string('id_user');
            $table->string('elaboro');
            $table->text('firma_elaboro');
            $table->string('reviso');
            $table->text('firma_reviso');
            $table->text('comentarios_generales');
            //Creacion de llave foranea
            $table->foreignId('id_sales')
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
