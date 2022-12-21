<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('code_sale', 20)->unique();
            $table->string('name_sale', 255);
            $table->string('sequence', 40);
            $table->text('invoice_address');
            $table->text('delivery_address');
            $table->dateTime('delivery_time')->nullable();
            $table->text('delivery_instructions');
            $table->dateTime('order_date')->nullable();
            $table->boolean('incidence');
            $table->boolean('sample_required')->default(false);
            $table->string('labeling', 191);
            $table->text('additional_information');
            $table->string('tariff', 50);
            $table->string('commercial_name');
            $table->string('commercial_email');
            $table->string('commercial_odoo_id');
            $table->decimal('total', 8, 2);
            $table->foreignId('status_id')->constrained();
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
        Schema::dropIfExists('sales');
    }
}
