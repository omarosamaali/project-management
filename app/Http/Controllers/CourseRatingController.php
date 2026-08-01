<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRating;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseRatingController extends Controller
{
    public function show(Course $course)
    {
        $payment = $this->resolvePayment($course);

        if (!$course->userNeedsRating(Auth::id())) {
            if ($course->userCompletedRating(Auth::id()) && $course->userCanGetCertificate(Auth::id())) {
                return redirect()->route('dashboard.courses.certificate', $payment);
            }

            return redirect()->route('dashboard.my_courses.index')
                ->with('error', 'التقييم غير متاح حالياً.');
        }

        $questions = config("course_rating.{$course->location_type}", []);

        return view('dashboard.courses.rating', compact('course', 'payment', 'questions'));
    }

    public function store(Request $request, Course $course)
    {
        $payment = $this->resolvePayment($course);

        if ($course->userCompletedRating(Auth::id())) {
            return redirect()->route('dashboard.courses.certificate', $payment);
        }

        $questions = config("course_rating.{$course->location_type}", []);
        $rules = [];
        $messages = [];

        foreach ($questions as $q) {
            $key = 'answers.' . $q['id'];
            $type = $q['type'] ?? 'text';

            if ($type === 'scale') {
                $min = (int) ($q['min'] ?? 1);
                $max = (int) ($q['max'] ?? 5);
                $rules[$key] = ($q['required'] ?? false)
                    ? "required|integer|min:{$min}|max:{$max}"
                    : "nullable|integer|min:{$min}|max:{$max}";
            } elseif ($type === 'boolean') {
                $rules[$key] = ($q['required'] ?? false)
                    ? 'required|in:1,0,yes,no'
                    : 'nullable|in:1,0,yes,no';
            } else {
                $rules[$key] = ($q['required'] ?? false)
                    ? 'required|string|max:2000'
                    : 'nullable|string|max:2000';
            }

            if ($q['required'] ?? false) {
                $messages[$key . '.required'] = 'هذا السؤال مطلوب';
            }
        }

        $validated = $request->validate($rules, $messages);

        CourseRating::updateOrCreate(
            [
                'course_id' => $course->id,
                'user_id' => Auth::id(),
            ],
            [
                'payment_id' => $payment->id,
                'answers' => $validated['answers'] ?? [],
                'completed_at' => now(),
            ]
        );

        if ($course->fresh()->userCanGetCertificate(Auth::id())) {
            return redirect()->route('dashboard.courses.certificate', $payment)
                ->with('success', 'شكراً لتقييمك. يمكنك الآن استخراج الشهادة.');
        }

        return redirect()->route('dashboard.my_courses.index')
            ->with('success', 'تم حفظ التقييم. شكراً لمشاركتك.');
    }

    protected function resolvePayment(Course $course): Payment
    {
        $query = Payment::where('user_id', Auth::id())
            ->where('course_id', $course->id);

        if (!$course->isRecorded()) {
            $query->where('is_attended', true);
        } else {
            $query->where('status', 'success');
        }

        $payment = $query->first();

        if (!$payment) {
            abort(403, 'التقييم متاح فقط للمشتركين.');
        }

        return $payment;
    }
}
