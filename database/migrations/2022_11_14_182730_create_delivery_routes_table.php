<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDeliveryRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_routes', function (Blueprint $table) {
            $table->id();
            $table->string('code_route');
            $table->boolean('is_active')->default(1)->nullable();
            $table->date('date_of_delivery');
            $table->string('status');
            $table->string('elaborated')->nullable();
            $table->string('revised')->nullable();
            $table->string('reason')->nullable();
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
        Schema::dropIfExists('delivery_routes');
    }
}
