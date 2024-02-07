<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryDevolutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_devolution', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_return', 16,2);
            $table->string('status');
            $table->foreignId('id_purchase')->references('id')->on('purchase_requests')->onDelete('cascade');
            $table->foreignId('id_user')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('history_devolution');
    }
}
