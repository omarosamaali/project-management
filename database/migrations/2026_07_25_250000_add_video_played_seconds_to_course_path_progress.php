<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_path_progress', function (Blueprint $table) {
            if (!Schema::hasColumn('course_path_progress', 'video_played_seconds')) {
                $table->unsignedInteger('video_played_seconds')->default(0)->after('video_watched_seconds');
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_path_progress', function (Blueprint $table) {
            if (Schema::hasColumn('course_path_progress', 'video_played_seconds')) {
                $table->dropColumn('video_played_seconds');
            }
        });
    }
};
