<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedInteger('required_exam_pass_count')->nullable()->after('exam_ended_at');
        });

        Schema::create('course_day_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->unsignedInteger('day_index');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('title')->nullable();
            $table->unsignedInteger('pass_score')->default(1);
            $table->unsignedInteger('duration_minutes')->default(30);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('skipped_at')->nullable();
            $table->foreignId('skipped_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['course_id', 'day_index', 'sort_order']);
        });

        Schema::create('course_day_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_day_exam_id')->constrained('course_day_exams')->cascadeOnDelete();
            $table->text('question');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('course_day_exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('course_day_exam_questions')->cascadeOnDelete();
            $table->string('answer');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('course_day_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_day_exam_id')->constrained('course_day_exams')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->unsignedInteger('score')->default(0);
            $table->boolean('passed')->default(false);
            $table->json('answers')->nullable();
            $table->json('shuffle_map')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['course_day_exam_id', 'user_id'], 'day_exam_user_unique');
        });

        Schema::create('course_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->json('answers')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
        });

        // Migrate legacy single-exam courses into Day 1 day exams
        $legacyCourses = DB::table('courses')->where('has_exam', true)->get();
        foreach ($legacyCourses as $course) {
            $questions = DB::table('course_exam_questions')
                ->where('course_id', $course->id)
                ->orderBy('sort_order')
                ->get();

            if ($questions->isEmpty()) {
                continue;
            }

            $dayExamId = DB::table('course_day_exams')->insertGetId([
                'course_id' => $course->id,
                'day_index' => 1,
                'sort_order' => 0,
                'title' => 'الاختبار النهائي',
                'pass_score' => max(1, (int) ($course->exam_pass_score ?? 1)),
                'duration_minutes' => max(1, (int) ($course->exam_duration_minutes ?? 30)),
                'started_at' => $course->exam_started_at,
                'ended_at' => $course->exam_ended_at,
                'skipped_at' => null,
                'skipped_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $questionIdMap = [];
            foreach ($questions as $qi => $q) {
                $newQid = DB::table('course_day_exam_questions')->insertGetId([
                    'course_day_exam_id' => $dayExamId,
                    'question' => $q->question,
                    'sort_order' => $q->sort_order ?? $qi,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $questionIdMap[$q->id] = $newQid;

                $answers = DB::table('course_exam_answers')
                    ->where('question_id', $q->id)
                    ->orderBy('sort_order')
                    ->get();

                $answerIdMap = [];
                foreach ($answers as $ai => $a) {
                    $newAid = DB::table('course_day_exam_answers')->insertGetId([
                        'question_id' => $newQid,
                        'answer' => $a->answer,
                        'is_correct' => (bool) $a->is_correct,
                        'sort_order' => $a->sort_order ?? $ai,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $answerIdMap[$a->id] = $newAid;
                }

                // Remap stored attempt answers later using question/answer maps per attempt
                unset($answerIdMap);
            }

            $attempts = DB::table('course_exam_attempts')->where('course_id', $course->id)->get();
            foreach ($attempts as $attempt) {
                $answersJson = $attempt->answers;
                $remapped = null;
                if ($answersJson) {
                    $decoded = json_decode($answersJson, true);
                    if (is_array($decoded)) {
                        $remapped = [];
                        foreach ($decoded as $oldQid => $payload) {
                            $newQid = $questionIdMap[$oldQid] ?? null;
                            if (!$newQid) {
                                continue;
                            }
                            $remapped[$newQid] = $payload;
                        }
                    }
                }

                DB::table('course_day_exam_attempts')->insert([
                    'course_day_exam_id' => $dayExamId,
                    'course_id' => $course->id,
                    'user_id' => $attempt->user_id,
                    'payment_id' => $attempt->payment_id,
                    'score' => $attempt->score ?? 0,
                    'passed' => (bool) ($attempt->passed ?? false),
                    'answers' => $remapped ? json_encode($remapped) : $answersJson,
                    'shuffle_map' => $attempt->shuffle_map ?? null,
                    'submitted_at' => $attempt->submitted_at,
                    'created_at' => $attempt->created_at ?? now(),
                    'updated_at' => $attempt->updated_at ?? now(),
                ]);
            }

            DB::table('courses')->where('id', $course->id)->update([
                'required_exam_pass_count' => 1,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_ratings');
        Schema::dropIfExists('course_day_exam_attempts');
        Schema::dropIfExists('course_day_exam_answers');
        Schema::dropIfExists('course_day_exam_questions');
        Schema::dropIfExists('course_day_exams');

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('required_exam_pass_count');
        });
    }
};
