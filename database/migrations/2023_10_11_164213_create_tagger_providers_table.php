<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaggerProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*         'odoo_id',
        'name_user',
        'email',
        'name_provider', */
        Schema::create('tagger_providers', function (Blueprint $table) {
            $table->id();
            $table->integer('odoo_id');
            $table->string('name_user');
            $table->string('email');
            $table->string('name_provider');
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
        Schema::dropIfExists('tagger_providers');
    }
}
