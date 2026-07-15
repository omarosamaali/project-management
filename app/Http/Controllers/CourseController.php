<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Service;
use App\Models\Payment;
use App\Models\MyStore;
use App\Models\CourseExamQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('service')
            ->latest()
            ->paginate(10);

        return view('dashboard.courses.index', compact('courses'));
    }

    public function create()
    {
        $services = Service::all();
        return view('dashboard.courses.create', compact('services'));
    }

    protected function prepareJsonFields(Request $request, array &$data)
    {
        // المتطلبات
        $requirements = [];
        if ($request->filled('requirements_ar') && $request->filled('requirements_en')) {
            foreach ($request->requirements_ar as $index => $ar) {
                $en = $request->requirements_en[$index] ?? '';
                if (trim($ar) || trim($en)) {
                    $requirements[] = [
                        'ar' => trim($ar),
                        'en' => trim($en),
                    ];
                }
            }
        }
        $data['requirements'] = $requirements;

        // المميزات
        $features = [];
        if ($request->filled('features_ar') && $request->filled('features_en')) {
            foreach ($request->features_ar as $index => $ar) {
                $en = $request->features_en[$index] ?? '';
                if (trim($ar) || trim($en)) {
                    $features[] = [
                        'ar' => trim($ar),
                        'en' => trim($en),
                    ];
                }
            }
        }
        $data['features'] = $features;

        // الأزرار
        $buttons = [];
        if ($request->filled('buttons_text_ar')) {
            foreach ($request->buttons_text_ar as $index => $text_ar) {
                $text_en = $request->buttons_text_en[$index] ?? '';
                $link = $request->buttons_link[$index] ?? '';
                $color = $request->buttons_color[$index] ?? '#3B82F6';
                $needsLogin = filter_var($request->buttons_needs_login[$index] ?? false, FILTER_VALIDATE_BOOLEAN);

                if (trim($text_ar) || trim($text_en)) {
                    $buttons[] = [
                        'text_ar' => trim($text_ar),
                        'text_en' => trim($text_en),
                        'link' => $link,
                        'color' => $color,
                        'needs_login' => $needsLogin,
                    ];
                }
            }
        }
        $data['buttons'] = $buttons;

        // أيام الراحة - الجديد
        $data['rest_days'] = $request->input('rest_days', []);
    }

    public function store(Request $request)
    {
        $data = $this->validateCourse($request);
        $this->prepareJsonFields($request, $data);

        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('courses/main', 'public');
        }

        if ($request->hasFile('images')) {
            $imagesPaths = [];
            foreach ($request->file('images') as $image) {
                $imagesPaths[] = $image->store('courses/gallery', 'public');
            }
            $data['images'] = $imagesPaths;
        }

        $course = DB::transaction(function () use ($data, $request) {
            $course = Course::create($data);
            $this->syncExamQuestions($course, $request);
            return $course;
        });

        return redirect()->route('dashboard.courses.index')->with('success', 'تم إضافة الدورة بنجاح.');
    }
    public function show(Course $course)
    {
        // نفس حالات الاشتراك المعتمدة في isUserEnrolled (يشمل pending إلى اكتمال الدفع)
        $course->load([
            'payments' => function ($query) {
                $query->whereIn('status', ['completed', 'success', 'paid', 'active', 'pending'])
                    ->with('user')
                    ->latest();
            },
            'examQuestions.answers',
            'examAttempts',
        ]);

        return view('dashboard.courses.show', compact('course'));
    }
    public function edit(Course $course)
    {
        $services = Service::all();
        $course->load('examQuestions.answers');
        return view('dashboard.courses.edit', compact('course', 'services'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $this->validateCourse($request, $course->id);
        $this->prepareJsonFields($request, $data);

        if ($request->hasFile('main_image')) {
            if ($course->main_image) {
                Storage::disk('public')->delete($course->main_image);
            }
            $data['main_image'] = $request->file('main_image')->store('courses/main', 'public');
        }

        if ($request->hasFile('images')) {
            if ($course->images) {
                foreach ($course->images as $old_img) {
                    Storage::disk('public')->delete($old_img);
                }
            }
            $imagesPaths = [];
            foreach ($request->file('images') as $image) {
                $imagesPaths[] = $image->store('courses/gallery', 'public');
            }
            $data['images'] = $imagesPaths;
        }

        // Don't reset exam timestamps from form
        unset($data['exam_started_at'], $data['exam_ended_at']);

        DB::transaction(function () use ($course, $data, $request) {
            $course->update($data);
            $this->syncExamQuestions($course->fresh(), $request);
        });

        return redirect()->route('dashboard.courses.index')->with('success', 'تم تحديث بيانات الدورة بنجاح.');
    }

    public function destroy(Course $course)
    {
        if ($course->main_image) {
            Storage::disk('public')->delete($course->main_image);
        }

        if ($course->images) {
            foreach ($course->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $course->delete();

        return redirect()->route('dashboard.courses.index')->with('success', 'تم حذف الدورة وملفاتها بنجاح.');
    }

    protected function validateCourse(Request $request, $id = null)
    {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'counter' => 'required|integer|min:0',
            'count_days' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'last_date' => 'required|date|before_or_equal:start_date',
            'location_type' => 'required|in:online,on_site',
            'online_link' => 'required_if:location_type,online|nullable|url',
            'venue_name' => 'required_if:location_type,on_site|nullable|string|max:255',
            'venue_map_url' => 'nullable|url',
            'venue_details' => 'nullable|string',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',

            // المتطلبات والمميزات والأزرار
            'requirements_ar.*' => 'required|string|max:255',
            'requirements_en.*' => 'required|string|max:255',
            'features_ar.*' => 'required|string|max:255',
            'features_en.*' => 'required|string|max:255',
            'buttons_text_ar.*' => 'nullable|string|max:100',
            'buttons_text_en.*' => 'nullable|string|max:100',
            'buttons_link.*' => 'nullable|url|max:500',
            'buttons_color.*' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/i',
            'buttons_needs_login.*' => 'nullable|in:0,1',

            // أيام الراحة - الجديد
            'rest_days' => 'nullable|array',
            'rest_days.*' => 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',

            'service_id' => 'nullable|exists:services,id',
            'status' => 'required|in:active,inactive',
            'has_exam' => 'nullable|boolean',
            'exam_pass_score' => 'nullable|integer|min:1',
            'exam_duration_minutes' => 'nullable|integer|min:1|max:600',
            'exam_questions' => 'nullable|array',
            'exam_questions.*.question' => 'nullable|string|max:1000',
            'exam_questions.*.answers' => 'nullable|array|min:1|max:6',
            'exam_questions.*.answers.*' => 'nullable|string|max:500',
            'exam_questions.*.correct' => 'nullable|integer|min:0|max:5',
            'main_image' => ($id ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'requirements_ar.*.required' => 'كل متطلب بالعربية مطلوب',
            'features_ar.*.required' => 'كل ميزة بالعربية مطلوبة',
            'main_image.required' => 'الصورة الرئيسية مطلوبة عند الإضافة',
            'rest_days.*.in' => 'يوم الراحة المحدد غير صحيح',
        ]);

        $data['has_exam'] = $request->boolean('has_exam');
        if (!$data['has_exam']) {
            $data['exam_pass_score'] = null;
            $data['exam_duration_minutes'] = null;
        }

        return $data;
    }

    protected function syncExamQuestions(Course $course, Request $request): void
    {
        if (!$request->boolean('has_exam')) {
            $course->examQuestions()->each(function (CourseExamQuestion $question) {
                $question->answers()->delete();
                $question->delete();
            });
            $course->update(['has_exam' => false, 'exam_pass_score' => null, 'exam_duration_minutes' => null]);
            return;
        }

        if ($course->exam_started_at) {
            // Locked after start — only pass score can stay as saved via update
            return;
        }

        $questions = $request->input('exam_questions', []);
        $cleaned = [];

        foreach ($questions as $qIndex => $qData) {
            $text = trim($qData['question'] ?? '');
            $answers = array_values(array_filter(
                array_map('trim', $qData['answers'] ?? []),
                fn ($a) => $a !== ''
            ));
            $correct = isset($qData['correct']) ? (int) $qData['correct'] : -1;

            if ($text === '' && empty($answers)) {
                continue;
            }

            if ($text === '' || count($answers) < 1 || count($answers) > 6) {
                throw ValidationException::withMessages([
                    "exam_questions.{$qIndex}.question" => 'كل سؤال يحتاج نصاً ومن 1 إلى 6 إجابات.',
                ]);
            }

            if ($correct < 0 || $correct >= count($answers)) {
                throw ValidationException::withMessages([
                    "exam_questions.{$qIndex}.correct" => 'يجب تحديد الإجابة الصحيحة لكل سؤال.',
                ]);
            }

            $cleaned[] = [
                'question' => $text,
                'answers' => $answers,
                'correct' => $correct,
            ];
        }

        if (count($cleaned) < 1) {
            throw ValidationException::withMessages([
                'exam_questions' => 'يجب إضافة سؤال واحد على الأقل للاختبار.',
            ]);
        }

        $passScore = (int) $request->input('exam_pass_score', 1);
        if ($passScore < 1 || $passScore > count($cleaned)) {
            throw ValidationException::withMessages([
                'exam_pass_score' => 'درجة النجاح يجب أن تكون بين 1 وعدد الأسئلة.',
            ]);
        }

        $duration = (int) $request->input('exam_duration_minutes', 0);
        if ($duration < 1) {
            throw ValidationException::withMessages([
                'exam_duration_minutes' => 'يجب تحديد مدة الاختبار بالدقائق (دقيقة واحدة على الأقل).',
            ]);
        }

        DB::transaction(function () use ($course, $cleaned, $passScore, $duration) {
            $course->examQuestions()->each(function (CourseExamQuestion $question) {
                $question->answers()->delete();
                $question->delete();
            });

            foreach ($cleaned as $qi => $qData) {
                $question = $course->examQuestions()->create([
                    'question' => $qData['question'],
                    'sort_order' => $qi,
                ]);

                foreach ($qData['answers'] as $ai => $answerText) {
                    $question->answers()->create([
                        'answer' => $answerText,
                        'is_correct' => $ai === $qData['correct'],
                        'sort_order' => $ai,
                    ]);
                }
            }

            $course->update([
                'has_exam' => true,
                'exam_pass_score' => $passScore,
                'exam_duration_minutes' => $duration,
            ]);
        });
    }

    public function startExam(Course $course)
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403);
        }

        if (!$course->has_exam) {
            return back()->with('error', 'هذه الدورة لا تحتوي على اختبار.');
        }

        if ($course->examQuestions()->count() < 1) {
            return back()->with('error', 'لا يمكن بدء اختبار بدون أسئلة.');
        }

        if ($course->exam_started_at && !$course->exam_ended_at) {
            return back()->with('error', 'تم بدء الاختبار مسبقاً وهو جارٍ الآن.');
        }

        if ($course->exam_ended_at) {
            return back()->with('error', 'انتهى هذا الاختبار ولا يمكن بدؤه من جديد.');
        }

        $course->update([
            'exam_started_at' => now(),
            'exam_ended_at' => null,
        ]);

        return back()->with('success', 'تم بدء الاختبار. سيتم تحويل الحضور تلقائياً لصفحة الاختبار.');
    }

    public function endExam(Course $course)
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403);
        }

        if (!$course->has_exam) {
            return back()->with('error', 'هذه الدورة لا تحتوي على اختبار.');
        }

        if (!$course->exam_started_at) {
            return back()->with('error', 'لم يبدأ الاختبار بعد.');
        }

        if ($course->exam_ended_at) {
            return back()->with('error', 'تم إنهاء الاختبار مسبقاً.');
        }

        $course->update(['exam_ended_at' => now()]);

        return back()->with('success', 'تم إنهاء الاختبار.');
    }

    public function payments(Course $course)
    {
        $payments = $course->students()->get();
        return view('dashboard.courses.payments', compact('course', 'payments'));
    }

    public function userShow(Course $course)
    {
        // حساب total_participants للـ course الحالي
        $course->loadCount(['payments' => function ($query) {
            $query->whereIn('status', ['completed', 'success', 'paid']);
        }]);
        $course->total_participants = ($course->payments_count ?? 0) + ($course->counter ?? 0);

        $serivce_id = $course->service_id;

        // جلب الدورات المرتبطة مع العدد
        $related_courses = Course::where('service_id', $serivce_id)
            ->where('id', '!=', $course->id)
            ->where('status', 'active')
            ->withCount(['payments' => function ($query) {
                $query->whereIn('status', ['completed', 'success', 'paid']);
            }])
            ->limit(6)
            ->get()
            ->each(function ($item) {
                // إضافة total_participants لكل عنصر
                $item->total_participants = ($item->payments_count ?? 0) + ($item->counter ?? 0);
            });

        $is_enrolled = $course->isUserEnrolled();

        return view('course.show', compact('course', 'is_enrolled', 'related_courses'));
    }

    public function userShowStore(MyStore $store)
    {
        $serivce_id = $store->service_id;
        $related_stores = MyStore::where('service_id', $serivce_id)
            ->where('id', '!=', $store->id)
            ->where('status', 'نشط')
            ->limit(6)
            ->get();

        $is_enrolled = $store->isUserEnrolled();

        return view('store.show', compact('store', 'is_enrolled', 'related_stores'));
    }

    public function toggleAttendance($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->is_attended = !$payment->is_attended;
        $payment->save();

        return back()->with('success', 'تم تحديث حالة الحضور بنجاح');
    }

    public function bulkAttendance(Request $request, Course $course)
    {
        $data = $request->validate([
            'payment_ids' => 'required|array|min:1',
            'payment_ids.*' => 'integer|exists:payments,id',
            'action' => 'required|in:attend,unattend',
        ]);

        $markAsAttended = $data['action'] === 'attend';

        $updated = Payment::where('course_id', $course->id)
            ->whereIn('id', $data['payment_ids'])
            ->where('is_attended', !$markAsAttended)
            ->update(['is_attended' => $markAsAttended]);

        if ($updated === 0) {
            return back()->with(
                'error',
                $markAsAttended
                    ? 'لم يتم تحديد مشتركين بحاجة للتحضير'
                    : 'لم يتم تحديد مشتركين تم تحضيرهم مسبقاً'
            );
        }

        return back()->with(
            'success',
            $markAsAttended
                ? "تم تسجيل حضور {$updated} مشترك بنجاح"
                : "تم إلغاء حضور {$updated} مشترك بنجاح"
        );
    }

    public function showCertificate($paymentId)
    {
        $payment = Payment::with(['user', 'course'])->findOrFail($paymentId);

        if (!$payment->is_attended) {
            return back()->with('error', 'لا يمكن استخراج شهادة لمن لم يحضر');
        }

        $course = $payment->course;
        if ($course && $course->has_exam && !$course->userPassedExam($payment->user_id)) {
            return back()->with('error', 'الشهادة متاحة فقط بعد اجتياز الاختبار');
        }

        return view('dashboard.courses.certificate', compact('payment'));
    }
}
