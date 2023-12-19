<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('notification_template_id');
            $table->unsignedBigInteger('notification_type_id'); // offers or news (( notification type ))
            $table->string('user_type');
            $table->text('content');
            $table->date('send_at');
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('notifications_jobs');
    }
}
