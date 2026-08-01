<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_path_items', function (Blueprint $table) {
            $table->string('video_embed_url', 1000)->nullable()->after('video_path');
        });
    }

    public function down(): void
    {
        Schema::table('course_path_items', function (Blueprint $table) {
            $table->dropColumn('video_embed_url');
        });
    }
};
