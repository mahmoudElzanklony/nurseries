<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRejectedFinancialOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rejected_financial_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_reconciliation_id')->constrained('financial_reconciliations')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('order_id');
            $table->string('oder_type');
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
        Schema::dropIfExists('rejected_financial_orders');
    }
}
