<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->string('code_inspection');
            $table->foreignId('sale_id')->constrained();
            $table->foreignId('user_created_id')->references('id')->on('users');
            $table->dateTime('date_inspection');
            $table->string('type_product');
            $table->text('observations');
            $table->string('user_created');
            $table->text('user_signature_created');
            $table->string('user_reviewed');
            $table->text('user_signature_reviewed');
            $table->integer('quantity_revised');
            $table->integer('quantity_denied');
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
        Schema::dropIfExists('inspections');
    }
}
