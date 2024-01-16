<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomOrdersSelectedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_orders_selected_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_order_id')->constrained('custom_orders')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('custom_orders_sellers_replies_id')->constrained('custom_orders_sellers_replies')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('quantity');
            $table->integer('price'); // total price
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
        Schema::dropIfExists('custom_orders_selected_products');
    }
}
