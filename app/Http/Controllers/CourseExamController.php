<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseDayExam;
use App\Models\CourseDayExamAttempt;
use App\Models\Payment;
use App\Services\WhatsAppOTPService;
use App\Support\ShufflesExamQuestions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CourseExamController extends Controller
{
    use ShufflesExamQuestions;

    /**
     * Attended student with a running day exam and no submitted attempt for it.
     */
    public static function findPendingExamPayment(int $userId): ?array
    {
        $payments = Payment::query()
            ->where('user_id', $userId)
            ->where('is_attended', true)
            ->whereNotNull('course_id')
            ->with(['course.dayExams'])
            ->latest()
            ->get();

        foreach ($payments as $payment) {
            $course = $payment->course;
            if (!$course || $course->isRecorded() || !$course->usesDayExams()) {
                continue;
            }

            $running = $course->runningDayExam();
            if (!$running) {
                continue;
            }

            $submitted = CourseDayExamAttempt::where('course_day_exam_id', $running->id)
                ->where('user_id', $userId)
                ->whereNotNull('submitted_at')
                ->exists();

            if (!$submitted) {
                return ['payment' => $payment, 'dayExam' => $running];
            }
        }

        return null;
    }

    public function pendingCheck()
    {
        $user = Auth::user();

        if (!$user || $user->role === 'admin') {
            return response()->json(['redirect' => null]);
        }

        $pending = self::findPendingExamPayment($user->id);

        if (!$pending) {
            return response()->json(['redirect' => null]);
        }

        /** @var CourseDayExam $dayExam */
        $dayExam = $pending['dayExam'];
        $course = $pending['payment']->course;

        return response()->json([
            'redirect' => route('dashboard.courses.exam.take', [$course, $dayExam]),
            'course_name' => $course->name_ar ?? '',
        ]);
    }

    public function take(Course $course, CourseDayExam $dayExam)
    {
        abort_unless((int) $dayExam->course_id === (int) $course->id, 404);
        $payment = $this->authorizeExamAccess($course, $dayExam);

        $existing = CourseDayExamAttempt::where('course_day_exam_id', $dayExam->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing && $existing->isSubmitted()) {
            return redirect()->route('dashboard.courses.exam.result', [$course, $dayExam]);
        }

        $questions = $dayExam->questions()->with('answers')->get();

        if ($questions->isEmpty()) {
            return redirect()->route('dashboard.my_courses.index')
                ->with('error', 'لا توجد أسئلة للاختبار حالياً.');
        }

        if (!$existing) {
            $existing = CourseDayExamAttempt::create([
                'course_day_exam_id' => $dayExam->id,
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
            $existing->update(['shuffle_map' => $this->buildShuffleMap($questions)]);
        }

        $questions = $this->applyShuffleMap($questions, $existing->shuffle_map);

        $durationMinutes = max(1, (int) ($dayExam->duration_minutes ?? 30));
        $endsAt = $existing->created_at->copy()->addMinutes($durationMinutes);
        $remainingSeconds = max(0, $endsAt->getTimestamp() - now()->getTimestamp());

        if ($remainingSeconds <= 0) {
            $this->finalizeAttempt($course, $dayExam, $payment, $existing, [], timedOut: true);
            return redirect()->route('dashboard.courses.exam.result', [$course, $dayExam])
                ->with('error', 'انتهى وقت الاختبار.');
        }

        return view('dashboard.courses.exam.take', compact(
            'course',
            'dayExam',
            'payment',
            'questions',
            'durationMinutes',
            'remainingSeconds',
            'endsAt'
        ));
    }

    public function submit(Request $request, Course $course, CourseDayExam $dayExam)
    {
        abort_unless((int) $dayExam->course_id === (int) $course->id, 404);
        $payment = $this->authorizeExamAccess($course, $dayExam);

        $existing = CourseDayExamAttempt::where('course_day_exam_id', $dayExam->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing && $existing->isSubmitted()) {
            return redirect()->route('dashboard.courses.exam.result', [$course, $dayExam]);
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
            $existing = CourseDayExamAttempt::create([
                'course_day_exam_id' => $dayExam->id,
                'course_id' => $course->id,
                'user_id' => Auth::id(),
                'payment_id' => $payment->id,
                'score' => 0,
                'passed' => false,
            ]);
        }

        $durationMinutes = max(1, (int) ($dayExam->duration_minutes ?? 30));
        $endsAt = $existing->created_at->copy()->addMinutes($durationMinutes);
        if (now()->greaterThan($endsAt->copy()->addSeconds(15))) {
            $timedOut = true;
        }

        $answersInput = $request->input('answers', []) ?: [];

        if (!$timedOut) {
            $questionCount = $dayExam->questions()->count();
            $answeredCount = collect($answersInput)->filter()->count();
            if ($answeredCount < $questionCount) {
                return back()->with('error', 'يجب الإجابة على جميع الأسئلة قبل التسليم.');
            }
        }

        $this->finalizeAttempt($course, $dayExam, $payment, $existing, $answersInput, $timedOut);

        $redirect = redirect()->route('dashboard.courses.exam.result', [$course, $dayExam]);
        if ($timedOut) {
            $redirect->with('error', 'انتهى وقت الاختبار وتم التسليم تلقائياً.');
        }

        return $redirect;
    }

    public function result(Course $course, CourseDayExam $dayExam)
    {
        abort_unless((int) $dayExam->course_id === (int) $course->id, 404);
        $payment = $this->authorizeExamAccess($course, $dayExam, requireStarted: false);

        $attempt = CourseDayExamAttempt::where('course_day_exam_id', $dayExam->id)
            ->where('user_id', Auth::id())
            ->whereNotNull('submitted_at')
            ->firstOrFail();

        $totalQuestions = $dayExam->questions()->count();
        $course->load(['dayExams', 'ratings']);

        $needsRating = $course->userNeedsRating(Auth::id());
        $canCertificate = $course->userCanGetCertificate(Auth::id());
        $passedCount = $course->userPassedDayExamCount(Auth::id());
        $requiredPass = $course->effectiveRequiredExamPassCount();

        return view('dashboard.courses.exam.result', compact(
            'course',
            'dayExam',
            'payment',
            'attempt',
            'totalQuestions',
            'needsRating',
            'canCertificate',
            'passedCount',
            'requiredPass'
        ));
    }

    protected function finalizeAttempt(
        Course $course,
        CourseDayExam $dayExam,
        Payment $payment,
        CourseDayExamAttempt $attempt,
        array $answersInput,
        bool $timedOut = false
    ): void {
        $questions = $dayExam->questions()->with('answers')->get();
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

        $passed = $score >= (int) $dayExam->pass_score;

        $attempt->update([
            'payment_id' => $payment->id,
            'score' => $score,
            'passed' => $passed,
            'answers' => $storedAnswers,
            'submitted_at' => now(),
        ]);

        if ($passed) {
            $this->notifyExamSuccess($payment, $course, $score, $questions->count(), $dayExam);
        }
    }

    protected function notifyExamSuccess(
        Payment $payment,
        Course $course,
        int $score,
        int $totalQuestions,
        CourseDayExam $dayExam
    ): void {
        try {
            $payment->loadMissing('user');
            $user = $payment->user;

            if (!$user || empty($user->phone)) {
                return;
            }

            app(WhatsAppOTPService::class)->sendExamSuccessNotification(
                $user->phone,
                $user->name,
                $course->name_ar . ' — ' . $dayExam->displayTitle(),
                $score,
                $totalQuestions,
            );
        } catch (\Throwable $e) {
            Log::error('[WHATSAPP] فشل إرسال إشعار نجاح الاختبار', [
                'payment_id' => $payment->id,
                'course_id' => $course->id,
                'day_exam_id' => $dayExam->id,
                'user_id' => $payment->user_id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function authorizeExamAccess(
        Course $course,
        CourseDayExam $dayExam,
        bool $requireStarted = true
    ): Payment {
        abort_if($course->isRecorded() || !$course->usesDayExams(), 404);

        if ($requireStarted && !$dayExam->isRunning()) {
            if ($dayExam->isFinished()) {
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
