<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_actions', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->nullable();
            $table->string('payment_type')->nullable();
            $table->integer('money');
            $table->string('type'); // service , wallet
            $table->string('status'); // pending if insert using vodafone or bank or request to take money
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
        Schema::dropIfExists('payment_actions');
    }
}
