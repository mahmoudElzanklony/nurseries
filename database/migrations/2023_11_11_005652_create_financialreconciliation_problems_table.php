<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialreconciliationProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financialreconciliation_problems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_reconciliation_id')->constrained('financial_reconciliations')->onUpdate('cascade')->onDelete('cascade');
            $table->string('orders')->nullable();
            $table->string('custom_orders')->nullable();
            $table->text('content');
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
        Schema::dropIfExists('financialreconciliation_problems');
    }
}
