<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesHeadingQuestionsDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories_heading_questions_datas', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('category_heading_question_id')->unsigned();
            $table->foreign('category_heading_question_id','fk_category_heading_question_id')
                ->references('id')->on('categories_heading_questions')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->string('name');
            $table->string('type')->default('text');
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
        Schema::dropIfExists('categories_heading_questions_datas');
    }
}
