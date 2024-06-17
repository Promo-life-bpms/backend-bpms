<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code_order_oc');
            $table->json('code_order_ot');
            $table->string('code_sale');
            $table->string('description');
            $table->integer('product_id_oc');
            $table->json('product_id_ot');
            $table->date('planned_date');
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
        Schema::dropIfExists('orders_groups');
    }
}
