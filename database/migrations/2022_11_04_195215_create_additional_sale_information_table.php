<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalSaleInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_sale_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained();
            $table->text('client_name');
            $table->text('client_address');
            $table->text('client_contact');
            $table->string('warehouse_company');
            $table->text('warehouse_address');
            $table->string('delivery_policy');
            $table->boolean('schedule_change');
            $table->string('reason_for_change');
            $table->dateTime('planned_date');
            $table->dateTime('commitment_date');
            $table->dateTime('effective_date');
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
        Schema::dropIfExists('additional_sale_information');
    }
}
