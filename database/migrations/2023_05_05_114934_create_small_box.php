<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmallBox extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('centers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_status', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->tinyInteger('position');
            $table->string('description')->nullable();
            $table->boolean('is_cancelled');
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('user_has_center', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('center_id')->references('id')->on('centers');  
            $table->timestamps();         
        });

        Schema::create('spents', function (Blueprint $table) {
            $table->id();
            $table->string('concept');
            $table->foreignId('center_id')->references('id')->on('centers');
            $table->string('outgo_types'); 
            $table->string('expense_type');
            $table->timestamps();
        });

        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('company_id')->references('id')->on('companies');
            $table->foreignId('spent_id')->references('id')->on('spents');
            $table->string('description'); 
            $table->string('file');
            $table->string('commentary');
            $table->foreignId('purchase_status_id')->references('id')->on('purchase_status');
            $table->foreignId('payment_method_id')->references('id')->on('payment_methods');
            $table->decimal('total', 16,2);
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
        Schema::dropIfExists('purchase_requests');
        Schema::dropIfExists('spents');
        Schema::dropIfExists('user_has_center');
        Schema::dropIfExists('centers');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('purchase_status');
        Schema::dropIfExists('payment_methods');
    }
}
