<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_path_items', function (Blueprint $table) {
            if (!Schema::hasColumn('course_path_items', 'video_thumbnail_path')) {
                $table->string('video_thumbnail_path')->nullable()->after('video_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_path_items', function (Blueprint $table) {
            if (Schema::hasColumn('course_path_items', 'video_thumbnail_path')) {
                $table->dropColumn('video_thumbnail_path');
            }
        });
    }
};
