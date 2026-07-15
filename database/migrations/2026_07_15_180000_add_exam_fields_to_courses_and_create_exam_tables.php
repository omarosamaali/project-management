<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('has_exam')->default(false)->after('rest_days');
            $table->unsignedInteger('exam_pass_score')->nullable()->after('has_exam');
            $table->timestamp('exam_started_at')->nullable()->after('exam_pass_score');
        });

        Schema::create('course_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->text('question');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('course_exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('course_exam_questions')->cascadeOnDelete();
            $table->string('answer');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('course_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->unsignedInteger('score')->default(0);
            $table->boolean('passed')->default(false);
            $table->json('answers')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_exam_attempts');
        Schema::dropIfExists('course_exam_answers');
        Schema::dropIfExists('course_exam_questions');

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['has_exam', 'exam_pass_score', 'exam_started_at']);
        });
    }
};
