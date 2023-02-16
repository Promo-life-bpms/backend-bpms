<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemisionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('remisiones', function (Blueprint $table) {
            $table->id();
            $table->string('code_remission');
            $table->string('comments');
            $table->string('satisfaction');
            $table->string('delivered');
            $table->text('delivery_signature');
            $table->string('received');
            $table->text('signature_received');
            $table->foreignId('delivery_route_id')->reference('id')->on('delivery_routes');
            $table->foreignId('user_chofer_id')->reference('id')->on('users');
            $table->string('status')->default(1)->nullable();
            $table->text('evidence');
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
        Schema::dropIfExists('remisiones');
    }
}
