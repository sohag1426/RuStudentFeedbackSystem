<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assessment_events', function (Blueprint $table) {
            $table->string('session')->nullable()->after('group_id');
            $table->string('year')->nullable()->after('session');
            $table->string('semester')->nullable()->after('year');
            $table->decimal('feedback_percentage', 5, 2)->default(0)->after('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_events', function (Blueprint $table) {
            $table->dropColumn(['session', 'year', 'semester', 'feedback_percentage']);
        });
    }
};
