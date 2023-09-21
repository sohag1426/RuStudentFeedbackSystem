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
        Schema::table('assessments', function (Blueprint $table) {
            $table->foreign(['question_id'])->references(['id'])->on('questions')->onUpdate('NO ACTION')->onDelete('CASCADE');
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
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign('assessments_question_id_foreign');
            $table->dropForeign('assessments_event_id_foreign');
        });
    }
};
