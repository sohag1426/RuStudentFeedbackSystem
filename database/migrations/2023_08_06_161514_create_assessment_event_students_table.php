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
        Schema::create('assessment_event_students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id')->index('assessment_event_students_event_id_foreign');
            $table->unsignedBigInteger('department_id')->default(0);
            $table->unsignedBigInteger('group_id')->default(0);
            $table->integer('student_id');
            $table->string('name');
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
        Schema::dropIfExists('assessment_event_students');
    }
};
