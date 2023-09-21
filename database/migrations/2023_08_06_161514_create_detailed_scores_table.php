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
        Schema::create('detailed_scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('department_id')->default(0);
            $table->unsignedBigInteger('event_id')->index('detailed_scores_event_id_foreign');
            $table->unsignedBigInteger('question_id')->index('detailed_scores_question_id_foreign');
            $table->integer('assessment_count')->default(0);
            $table->string('score');
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
        Schema::dropIfExists('detailed_scores');
    }
};
