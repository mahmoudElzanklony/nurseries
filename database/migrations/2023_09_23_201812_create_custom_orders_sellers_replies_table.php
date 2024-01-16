<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomOrdersSellersRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_orders_sellers_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_orders_seller_id')->constrained('custom_orders_sellers')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->text('info');
            $table->integer('quantity');
            $table->float('product_price');
            $table->integer('days_delivery');
            $table->integer('delivery_price');
            //$table->string('client_reply')->default('pending');
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
        Schema::dropIfExists('custom_orders_sellers_replies');
    }
}
