<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusOTTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_o_t_s', function (Blueprint $table) {
            $table->id();
            $table->time('hora');
            $table->foreignId('id_order_purchases')
            ->nullable()
            ->constrained('order_purchases')
            ->cascadeOnUpdate()
            ->nullOnDelete();
            $table->string('status');
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
        Schema::dropIfExists('status_o_t_s');
    }
}
