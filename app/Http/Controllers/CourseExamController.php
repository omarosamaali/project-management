<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseExamAttempt;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Start the clock on first open (one attempt session)
        if (!$existing) {
            $existing = CourseExamAttempt::create([
                'course_id' => $course->id,
                'user_id' => Auth::id(),
                'payment_id' => $payment->id,
                'score' => 0,
                'passed' => false,
                'answers' => null,
                'submitted_at' => null,
            ]);
        }

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
}
