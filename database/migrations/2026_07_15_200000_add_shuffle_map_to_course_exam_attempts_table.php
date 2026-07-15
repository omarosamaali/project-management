<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_exam_attempts', function (Blueprint $table) {
            $table->json('shuffle_map')->nullable()->after('answers');
        });
    }

    public function down(): void
    {
        Schema::table('course_exam_attempts', function (Blueprint $table) {
            $table->dropColumn('shuffle_map');
        });
    }
};
