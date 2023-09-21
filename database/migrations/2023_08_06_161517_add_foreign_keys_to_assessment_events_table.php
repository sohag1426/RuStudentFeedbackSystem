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
        Schema::table('assessment_events', function (Blueprint $table) {
            $table->foreign(['group_id'])->references(['id'])->on('student_groups')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['course_id'])->references(['id'])->on('courses')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['teacher_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessment_events', function (Blueprint $table) {
            $table->dropForeign('assessment_events_group_id_foreign');
            $table->dropForeign('assessment_events_course_id_foreign');
            $table->dropForeign('assessment_events_teacher_id_foreign');
        });
    }
};
