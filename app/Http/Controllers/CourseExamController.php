<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseExamAttempt;
use App\Models\Payment;
use App\Services\WhatsAppOTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseExamController extends Controller
{
    /**
     * Attended student with a running exam and no submitted attempt.
     * Covers: exam just started, OR student marked attended while exam is running.
     */
    public static function findPendingExamPayment(int $userId): ?Payment
    {
        $attemptedCourseIds = CourseExamAttempt::where('user_id', $userId)
            ->whereNotNull('submitted_at')
            ->pluck('course_id');

        return Payment::query()
            ->where('user_id', $userId)
            ->where('is_attended', true)
            ->whereNotNull('course_id')
            ->when($attemptedCourseIds->isNotEmpty(), fn ($q) => $q->whereNotIn('course_id', $attemptedCourseIds))
            ->whereHas('course', function ($q) {
                $q->where('has_exam', true)
                    ->whereNotNull('exam_started_at')
                    ->whereNull('exam_ended_at');
            })
            ->with('course')
            ->latest()
            ->first();
    }

    /**
     * Fast poll endpoint for attended students (exam start or late attendance).
     */
    public function pendingCheck()
    {
        $user = Auth::user();

        if (!$user || $user->role === 'admin') {
            return response()->json(['redirect' => null]);
        }

        $payment = self::findPendingExamPayment($user->id);

        if (!$payment) {
            return response()->json(['redirect' => null]);
        }

        return response()->json([
            'redirect' => route('dashboard.courses.exam.take', $payment->course_id),
            'course_name' => $payment->course->name_ar ?? '',
        ]);
    }

    public function take(Course $course)
    {
        $payment = $this->authorizeExamAccess($course);

        $existing = CourseExamAttempt::where('course_id', $course->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing && $existing->isSubmitted()) {
            return redirect()->route('dashboard.courses.exam.result', $course);
        }

        $questions = $course->examQuestions()->with('answers')->get();

        if ($questions->isEmpty()) {
            return redirect()->route('dashboard.my_courses.index')
                ->with('error', 'لا توجد أسئلة للاختبار حالياً.');
        }

        // Start the clock on first open (one attempt session) + unique shuffle map
        if (!$existing) {
            $existing = CourseExamAttempt::create([
                'course_id' => $course->id,
                'user_id' => Auth::id(),
                'payment_id' => $payment->id,
                'score' => 0,
                'passed' => false,
                'answers' => null,
                'shuffle_map' => $this->buildShuffleMap($questions),
                'submitted_at' => null,
            ]);
        } elseif (empty($existing->shuffle_map)) {
            // Legacy attempts without a map — freeze a shuffle now so refresh stays stable
            $existing->update(['shuffle_map' => $this->buildShuffleMap($questions)]);
        }

        $questions = $this->applyShuffleMap($questions, $existing->shuffle_map);

        $durationMinutes = max(1, (int) ($course->exam_duration_minutes ?? 30));
        $endsAt = $existing->created_at->copy()->addMinutes($durationMinutes);
        $remainingSeconds = max(0, $endsAt->getTimestamp() - now()->getTimestamp());

        if ($remainingSeconds <= 0) {
            // Time already over — force submit empty/partial answers
            $this->finalizeAttempt($course, $payment, $existing, [], timedOut: true);
            return redirect()->route('dashboard.courses.exam.result', $course)
                ->with('error', 'انتهى وقت الاختبار.');
        }

        return view('dashboard.courses.exam.take', compact(
            'course',
            'payment',
            'questions',
            'durationMinutes',
            'remainingSeconds',
            'endsAt'
        ));
    }

    public function submit(Request $request, Course $course)
    {
        $payment = $this->authorizeExamAccess($course);

        $existing = CourseExamAttempt::where('course_id', $course->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing && $existing->isSubmitted()) {
            return redirect()->route('dashboard.courses.exam.result', $course);
        }

        $timedOut = $request->boolean('timed_out');

        $rules = [
            'answers' => $timedOut ? 'nullable|array' : 'required|array',
            'answers.*' => 'nullable|integer',
            'timed_out' => 'nullable|boolean',
        ];

        if (!$timedOut) {
            $rules['answers.*'] = 'required|integer';
        }

        $request->validate($rules, [
            'answers.required' => 'يجب الإجابة على جميع الأسئلة',
        ]);

        if (!$existing) {
            $existing = CourseExamAttempt::create([
                'course_id' => $course->id,
                'user_id' => Auth::id(),
                'payment_id' => $payment->id,
                'score' => 0,
                'passed' => false,
            ]);
        }

        // Server-side time check
        $durationMinutes = max(1, (int) ($course->exam_duration_minutes ?? 30));
        $endsAt = $existing->created_at->copy()->addMinutes($durationMinutes);
        if (now()->greaterThan($endsAt->copy()->addSeconds(15))) {
            $timedOut = true;
        }

        $answersInput = $request->input('answers', []) ?: [];

        if (!$timedOut) {
            $questionCount = $course->examQuestions()->count();
            $answeredCount = collect($answersInput)->filter()->count();
            if ($answeredCount < $questionCount) {
                return back()->with('error', 'يجب الإجابة على جميع الأسئلة قبل التسليم.');
            }
        }

        $this->finalizeAttempt($course, $payment, $existing, $answersInput, $timedOut);

        $redirect = redirect()->route('dashboard.courses.exam.result', $course);
        if ($timedOut) {
            $redirect->with('error', 'انتهى وقت الاختبار وتم التسليم تلقائياً.');
        }

        return $redirect;
    }

    public function result(Course $course)
    {
        $payment = $this->authorizeExamAccess($course, requireStarted: false);

        $attempt = CourseExamAttempt::where('course_id', $course->id)
            ->where('user_id', Auth::id())
            ->whereNotNull('submitted_at')
            ->firstOrFail();

        $totalQuestions = $course->examQuestions()->count();

        return view('dashboard.courses.exam.result', compact('course', 'payment', 'attempt', 'totalQuestions'));
    }

    protected function finalizeAttempt(
        Course $course,
        Payment $payment,
        CourseExamAttempt $attempt,
        array $answersInput,
        bool $timedOut = false
    ): void {
        $questions = $course->examQuestions()->with('answers')->get();
        $score = 0;
        $storedAnswers = [];

        foreach ($questions as $question) {
            $answerId = (int) ($answersInput[$question->id] ?? 0);
            $correct = $question->answers->firstWhere('is_correct', true);
            $isCorrect = $answerId > 0 && $correct && $correct->id === $answerId;
            if ($isCorrect) {
                $score++;
            }

            $storedAnswers[$question->id] = [
                'answer_id' => $answerId ?: null,
                'is_correct' => $isCorrect,
                'timed_out' => $timedOut && $answerId === 0,
            ];
        }

        $passed = $score >= (int) $course->exam_pass_score;

        $attempt->update([
            'payment_id' => $payment->id,
            'score' => $score,
            'passed' => $passed,
            'answers' => $storedAnswers,
            'submitted_at' => now(),
        ]);

        if ($passed) {
            $this->notifyExamSuccess($payment, $course, $score, $questions->count());
        }
    }

    protected function notifyExamSuccess(Payment $payment, Course $course, int $score, int $totalQuestions): void
    {
        try {
            $payment->loadMissing('user');
            $user = $payment->user;

            if (!$user || empty($user->phone)) {
                return;
            }

            app(WhatsAppOTPService::class)->sendExamSuccessNotification(
                $user->phone,
                $user->name,
                $course->name_ar,
                $score,
                $totalQuestions,
            );
        } catch (\Throwable $e) {
            Log::error('[WHATSAPP] فشل إرسال إشعار نجاح الاختبار', [
                'payment_id' => $payment->id,
                'course_id' => $course->id,
                'user_id' => $payment->user_id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function authorizeExamAccess(Course $course, bool $requireStarted = true): Payment
    {
        if (!$course->has_exam) {
            abort(404);
        }

        if ($requireStarted && !$course->isExamStarted()) {
            if ($course->exam_ended_at) {
                abort(403, 'انتهى الاختبار.');
            }
            abort(403, 'لم يبدأ الاختبار بعد.');
        }

        $payment = Payment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('is_attended', true)
            ->first();

        if (!$payment) {
            abort(403, 'الاختبار متاح فقط للحضور المسجلين.');
        }

        return $payment;
    }

    /**
     * Build a unique per-attempt shuffle for questions and each question's answers.
     *
     * @param  \Illuminate\Support\Collection  $questions
     */
    protected function buildShuffleMap($questions): array
    {
        $questionIds = $questions->pluck('id')->shuffle()->values()->all();
        $answerOrders = [];

        foreach ($questions as $question) {
            $answerOrders[(string) $question->id] = $question->answers
                ->pluck('id')
                ->shuffle()
                ->values()
                ->all();
        }

        return [
            'questions' => $questionIds,
            'answers' => $answerOrders,
        ];
    }

    /**
     * Reorder questions / answers according to a saved shuffle map.
     *
     * @param  \Illuminate\Support\Collection  $questions
     * @return \Illuminate\Support\Collection
     */
    protected function applyShuffleMap($questions, ?array $shuffleMap)
    {
        if (empty($shuffleMap['questions']) || !is_array($shuffleMap['questions'])) {
            return $questions->values();
        }

        $byId = $questions->keyBy('id');
        $ordered = collect();

        foreach ($shuffleMap['questions'] as $questionId) {
            $question = $byId->get($questionId);
            if (!$question) {
                continue;
            }

            $answerOrder = $shuffleMap['answers'][(string) $questionId]
                ?? $shuffleMap['answers'][$questionId]
                ?? null;

            if (is_array($answerOrder) && !empty($answerOrder)) {
                $answersById = $question->answers->keyBy('id');
                $shuffledAnswers = collect();
                foreach ($answerOrder as $answerId) {
                    if ($answersById->has($answerId)) {
                        $shuffledAnswers->push($answersById->get($answerId));
                    }
                }
                // Append any new answers not in the saved map
                foreach ($question->answers as $answer) {
                    if (!$shuffledAnswers->contains('id', $answer->id)) {
                        $shuffledAnswers->push($answer);
                    }
                }
                $question->setRelation('answers', $shuffledAnswers->values());
            } else {
                $question->setRelation('answers', $question->answers->shuffle()->values());
            }

            $ordered->push($question);
        }

        // Append any questions missing from the map (edge case if admin added questions mid-exam)
        foreach ($questions as $question) {
            if (!$ordered->contains('id', $question->id)) {
                $question->setRelation('answers', $question->answers->shuffle()->values());
                $ordered->push($question);
            }
        }

        return $ordered->values();
    }
}
