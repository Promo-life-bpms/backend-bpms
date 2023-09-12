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
            $table->string('satisfaction')->nullable();
            $table->string('delivered')->nullable();
            $table->text('delivery_signature')->nullable();
            $table->string('received')->nullable();
            $table->text('signature_received')->nullable();
            $table->foreignId('delivery_route_id')->reference('id')->on('delivery_routes');
            // Nullable
            $table->foreignId('sale_id')->reference('id')->on('sales')->nullable();
            $table->string('status')->default(1)->nullable();
            $table->text('evidence')->nullable();
            $table->string('code_sale')->nullable();
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
