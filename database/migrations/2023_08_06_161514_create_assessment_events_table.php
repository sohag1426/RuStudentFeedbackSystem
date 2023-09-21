<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->default(0);
            $table->unsignedBigInteger('department_id')->default(0);
            $table->unsignedBigInteger('teacher_id')->default(0)->index('assessment_events_teacher_id_foreign');
            $table->unsignedBigInteger('course_id')->index('assessment_events_course_id_foreign');
            $table->unsignedBigInteger('group_id')->index('assessment_events_group_id_foreign');
            $table->dateTime('start_time');
            $table->dateTime('stop_time');
            $table->integer('assessment_count')->default(0);
            $table->string('score')->default('undefined');
            $table->string('group_average')->nullable();
            $table->string('group_highest')->nullable();
            $table->string('group_lowest')->nullable();
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
        Schema::dropIfExists('assessment_events');
    }
};
