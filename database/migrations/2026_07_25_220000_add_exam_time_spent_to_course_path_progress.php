<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_path_progress', function (Blueprint $table) {
            if (!Schema::hasColumn('course_path_progress', 'exam_time_spent_seconds')) {
                $table->unsignedInteger('exam_time_spent_seconds')->nullable()->after('exam_answers');
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_path_progress', function (Blueprint $table) {
            if (Schema::hasColumn('course_path_progress', 'exam_time_spent_seconds')) {
                $table->dropColumn('exam_time_spent_seconds');
            }
        });
    }
};
