<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultiSelectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multi_selections', function (Blueprint $table) {
            $table->id();
            $table->string('selectionable_id')->nullable();
            $table->string('selectionable_type')->nullable();
            $table->string('ar_name'); // service , wallet
            $table->string('en_name'); // service , wallet
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
        Schema::dropIfExists('multi_selections');
    }
}
