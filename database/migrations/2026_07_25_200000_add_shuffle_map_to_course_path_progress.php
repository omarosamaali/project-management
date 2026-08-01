<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_path_progress', function (Blueprint $table) {
            $table->json('shuffle_map')->nullable()->after('exam_answers');
        });
    }

    public function down(): void
    {
        Schema::table('course_path_progress', function (Blueprint $table) {
            $table->dropColumn('shuffle_map');
        });
    }
};
