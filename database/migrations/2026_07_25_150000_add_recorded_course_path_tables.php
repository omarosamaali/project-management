<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expand location_type to include recorded courses
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE courses MODIFY location_type ENUM('online','on_site','recorded') NOT NULL DEFAULT 'online'");
        } else {
            // SQLite / others: recreate via change if supported; otherwise leave as string-compatible
            Schema::table('courses', function (Blueprint $table) {
                $table->string('location_type', 20)->default('online')->change();
            });
        }

        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedInteger('total_video_duration_seconds')->default(0)->after('video');
        });

        Schema::create('course_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['course_id', 'sort_order']);
        });

        Schema::create('course_path_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('course_units')->cascadeOnDelete();
            $table->enum('type', ['lesson', 'exam'])->default('lesson');
            $table->string('title');
            $table->string('video_path')->nullable();
            $table->unsignedInteger('video_duration_seconds')->nullable();
            $table->unsignedInteger('exam_pass_score')->nullable();
            $table->unsignedInteger('exam_duration_minutes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['unit_id', 'sort_order']);
        });

        Schema::create('course_path_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('path_item_id')->constrained('course_path_items')->cascadeOnDelete();
            $table->string('question', 1000);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('course_path_exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('course_path_exam_questions')->cascadeOnDelete();
            $table->string('answer', 500);
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('course_path_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('path_item_id')->constrained('course_path_items')->cascadeOnDelete();
            $table->unsignedInteger('video_watched_seconds')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->unsignedInteger('exam_score')->nullable();
            $table->boolean('exam_passed')->nullable();
            $table->json('exam_answers')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'path_item_id']);
            $table->index(['course_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_path_progress');
        Schema::dropIfExists('course_path_exam_answers');
        Schema::dropIfExists('course_path_exam_questions');
        Schema::dropIfExists('course_path_items');
        Schema::dropIfExists('course_units');

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('total_video_duration_seconds');
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE courses MODIFY location_type ENUM('online','on_site') NOT NULL DEFAULT 'online'");
        }
    }
};
