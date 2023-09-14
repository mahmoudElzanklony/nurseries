<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('category_id')->constrained()
                ->onUpdate('cascade')->onDelete('cascade');
            $table->string('ar_name');
            $table->string('en_name')->nullable();
            $table->text('ar_description');
            $table->text('en_description')->nullable();
            $table->integer('quantity');
            $table->float('main_price');
            $table->float('main_delivery_price');
            $table->softDeletes();
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
        Schema::dropIfExists('products');
    }
}
