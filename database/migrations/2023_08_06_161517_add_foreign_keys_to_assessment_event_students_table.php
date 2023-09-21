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
        Schema::table('assessment_event_students', function (Blueprint $table) {
            $table->foreign(['event_id'])->references(['id'])->on('assessment_events')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessment_event_students', function (Blueprint $table) {
            $table->dropForeign('assessment_event_students_event_id_foreign');
        });
    }
};
