<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersItemsFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_items_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('orders_items')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_feature_id')->constrained('products_features_prices')->onUpdate('cascade')->onDelete('cascade');
            $table->float('price');
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
        Schema::dropIfExists('orders_items_features');
    }
}
