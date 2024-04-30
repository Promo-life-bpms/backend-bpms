<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExManagerDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex_manager_departments', function (Blueprint $table) {
            $table->id();
            $table->integer('id_manager_has_department');
            $table->integer('user_who_deleted');
            $table->foreignId('ex_manager')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('id_department')->references('id')->on('departments')->onDelete('cascade');
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
        Schema::dropIfExists('ex_manager_departments');
    }
}
