<?php

namespace App\Http\Controllers;

use App\Jobs\AnnounceNewCourseJob;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Payment;
use App\Models\MyStore;
use App\Models\User;
use App\Models\CourseDayExam;
use App\Models\CourseDayExamQuestion;
use App\Models\CoursePathExamAnswer;
use App\Models\CoursePathExamQuestion;
use App\Models\CoursePathItem;
use App\Models\CourseRating;
use App\Models\CourseUnit;
use App\Models\CourseWishlist;
use App\Models\Setting;
use App\Support\YouTubeLive;
use App\Support\LessonVideoSource;
use App\Support\VideoDownloadGuard;
use App\Support\WatermarkedUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class CourseController extends Controller
{
    protected function authorizeCourseManager(?Course $course = null): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isTrainer()) {
            if ($course === null || $user->managesCourse($course)) {
                return;
            }
        }

        abort(403, 'غير مصرح لك بإدارة هذه الدورة.');
    }

    protected function authorizeCourseManageList(): void
    {
        if (!auth()->user()?->canManageCourses()) {
            abort(403, 'غير مصرح لك بإدارة الدورات.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeCourseManageList();

        $query = Course::with(['category', 'trainer'])->latest();

        if (auth()->user()->isTrainer()) {
            $query->where('trainer_id', auth()->id());
        }

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', '%'.$search.'%')
                    ->orWhere('name_en', 'like', '%'.$search.'%');
            });
        }

        $perPage = auth()->user()->usesAcademyShell() ? 9 : 10;
        $courses = $query->paginate($perPage)->withQueryString();

        return view('dashboard.courses.index', compact('courses'));
    }

    public function create()
    {
        $this->authorizeCourseManageList();
        $categories = CourseCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        $trainers = auth()->user()->isAdmin()
            ? User::where('role', 'trainer')->notBlocked()->orderBy('name')->get(['id', 'name', 'email'])
            : collect();

        $trainerProfitPercentage = Setting::academyTrainerProfitPercentage();

        return view('dashboard.courses.create', compact('categories', 'trainers', 'trainerProfitPercentage'));
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

        // مناسبة لمن (اختياري)
        $suitableFor = [];
        if ($request->filled('suitable_for_ar') || $request->filled('suitable_for_en')) {
            $arList = $request->input('suitable_for_ar', []);
            $enList = $request->input('suitable_for_en', []);
            foreach ($arList as $index => $ar) {
                $en = $enList[$index] ?? '';
                if (trim((string) $ar) || trim((string) $en)) {
                    $suitableFor[] = [
                        'ar' => trim((string) $ar),
                        'en' => trim((string) $en),
                    ];
                }
            }
        }
        $data['suitable_for'] = $suitableFor;

        $allowedLevels = collect(Course::levelOptions())->pluck('key')->all();
        $levels = array_values(array_intersect((array) $request->input('levels', []), $allowedLevels));
        $data['levels'] = $levels;

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

        // Always derive count_days from calendar dates (same day = 1)
        // Recorded courses have no schedule — keep sentinel count_days
        if (($data['location_type'] ?? $request->input('location_type')) === 'recorded') {
            $data['rest_days'] = [];
            $data['count_days'] = 0;
        } elseif (!empty($data['start_date']) && !empty($data['end_date'])) {
            $data['count_days'] = Course::computeCourseDays(
                $data['start_date'],
                $data['end_date'],
                $data['rest_days'] ?? []
            );
        }
    }

    public function store(Request $request)
    {
        $this->authorizeCourseManageList();

        $data = $this->validateCourse($request);
        $this->prepareJsonFields($request, $data);
        $data['trainer_id'] = $this->resolveTrainerId($request);
        $this->applyMeetingProvider($request, $data);

        if ($request->hasFile('main_image')) {
            $data['main_image'] = WatermarkedUpload::store($request->file('main_image'), 'courses/main');
        }

        if ($request->hasFile('images')) {
            $imagesPaths = [];
            foreach ($request->file('images') as $image) {
                $imagesPaths[] = WatermarkedUpload::store($image, 'courses/gallery');
            }
            $data['images'] = $imagesPaths;
        }

        if ($request->hasFile('video')) {
            $data['video'] = WatermarkedUpload::store(
                $request->file('video'),
                'courses/videos',
                \App\Support\VideoDownloadGuard::storageDisk()
            );
        }

        $course = DB::transaction(function () use ($data, $request) {
            $course = Course::create($data);
            $this->syncDayExams($course, $request);
            $this->syncEducationalPath($course, $request);
            return $course;
        });

        if ($course->status === 'active') {
            AnnounceNewCourseJob::dispatch($course->id);
        }

        return redirect()->route('dashboard.courses.index')->with('success', 'تم إضافة الدورة بنجاح.');
    }

    protected function resolveTrainerId(Request $request, ?Course $course = null): ?int
    {
        $user = auth()->user();

        if ($user->isTrainer()) {
            return (int) $user->id;
        }

        // Admin: optional assignment
        if ($request->filled('trainer_id')) {
            $trainerId = (int) $request->input('trainer_id');
            $exists = User::where('id', $trainerId)->where('role', 'trainer')->exists();
            return $exists ? $trainerId : null;
        }

        // Keep existing on update if admin cleared nothing explicitly
        if ($course && $request->has('trainer_id') && $request->input('trainer_id') === '') {
            return null;
        }

        return $course?->trainer_id;
    }
    public function show(Course $course)
    {
        $this->authorizeCourseManager($course);

        $course->load([
            'payments' => function ($query) {
                $query->whereIn('status', ['completed', 'success', 'paid', 'active', 'pending'])
                    ->with('user')
                    ->latest();
            },
            'dayExams.questions.answers',
            'dayExamAttempts',
            'ratings.user',
            'trainer',
            'category',
            'units.items.examQuestions.answers',
        ]);

        return view('dashboard.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $this->authorizeCourseManager($course);
        $categories = CourseCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        $course->load(['dayExams.questions.answers', 'units.items.examQuestions.answers']);
        $trainers = auth()->user()->isAdmin()
            ? User::where('role', 'trainer')->notBlocked()->orderBy('name')->get(['id', 'name', 'email'])
            : collect();

        $trainerProfitPercentage = Setting::academyTrainerProfitPercentage();

        return view('dashboard.courses.edit', compact('course', 'categories', 'trainers', 'trainerProfitPercentage'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorizeCourseManager($course);

        $data = $this->validateCourse($request, $course->id);
        $this->prepareJsonFields($request, $data);
        $data['trainer_id'] = $this->resolveTrainerId($request, $course);
        $this->applyMeetingProvider($request, $data, $course);

        $settingsLocked = $course->hasBegun();

        if ($settingsLocked) {
            // Preserve final settings (status + day exams) after the course has started
            unset(
                $data['status'],
                $data['required_exam_pass_count'],
                $data['has_exam'],
                $data['exam_pass_score'],
                $data['exam_duration_minutes']
            );
        }

        if ($request->hasFile('main_image')) {
            if ($course->main_image) {
                Storage::disk('public')->delete($course->main_image);
            }
            $data['main_image'] = WatermarkedUpload::store($request->file('main_image'), 'courses/main');
        }

        if ($request->hasFile('images')) {
            if ($course->images) {
                foreach ($course->images as $old_img) {
                    Storage::disk('public')->delete($old_img);
                }
            }
            $imagesPaths = [];
            foreach ($request->file('images') as $image) {
                $imagesPaths[] = WatermarkedUpload::store($image, 'courses/gallery');
            }
            $data['images'] = $imagesPaths;
        }

        if ($request->boolean('remove_video') && !$request->hasFile('video')) {
            if ($course->video) {
                \App\Support\VideoDownloadGuard::deleteStored($course->video);
            }
            $data['video'] = null;
        } elseif ($request->hasFile('video')) {
            if ($course->video) {
                \App\Support\VideoDownloadGuard::deleteStored($course->video);
            }
            $data['video'] = WatermarkedUpload::store(
                $request->file('video'),
                'courses/videos',
                \App\Support\VideoDownloadGuard::storageDisk()
            );
        }

        // Don't reset exam timestamps from form
        unset($data['exam_started_at'], $data['exam_ended_at']);

        DB::transaction(function () use ($course, $data, $request, $settingsLocked) {
            $course->update($data);
            if (!$settingsLocked) {
                $this->syncDayExams($course->fresh(), $request);
            }
            $this->syncEducationalPath($course->fresh(), $request);
        });


        $message = $settingsLocked
            ? 'تم تحديث بيانات الدورة بنجاح. (الإعدادات النهائية لم تُغيَّر لأن الدورة قد بدأت)'
            : 'تم تحديث بيانات الدورة بنجاح.';

        return redirect()->route('dashboard.courses.index')->with('success', $message);
    }

    public function destroy(Course $course)
    {
        $this->authorizeCourseManager($course);

        if ($course->main_image) {
            Storage::disk('public')->delete($course->main_image);
        }

        if ($course->images) {
            foreach ($course->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        if ($course->video) {
            VideoDownloadGuard::deleteStored($course->video);
        }

        $this->deleteEducationalPath($course);

        $course->delete();

        return redirect()->route('dashboard.courses.index')->with('success', 'تم حذف الدورة وملفاتها بنجاح.');
    }

    /**
     * Protected promo/course video stream (signed URL; blocks download managers).
     */
    public function streamPromo(Request $request, Course $course)
    {
        abort_unless($request->hasValidSignature(), 403, 'Invalid or expired video link.');
        abort_unless(filled($course->video), 404);

        $path = VideoDownloadGuard::absolutePath($course->video);
        abort_unless($path, 404);

        return VideoDownloadGuard::fileResponse($path);
    }

    protected function validateCourse(Request $request, $id = null)
    {
        $priceRules = ['required', 'numeric', 'min:0'];
        $trainerMaxPrice = (float) config('courses.trainer_max_price', 400);
        if (auth()->user()?->isTrainer() && ! auth()->user()?->isAdmin()) {
            $priceRules[] = 'max:'.$trainerMaxPrice;
        }

        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'price' => $priceRules,
            'counter' => 'required|integer|min:0',
            'count_days' => 'exclude_if:location_type,recorded|required|integer|min:0',
            'start_date' => 'exclude_if:location_type,recorded|required|date',
            'end_date' => 'exclude_if:location_type,recorded|required|date|after_or_equal:start_date',
            'last_date' => 'exclude_if:location_type,recorded|required|date|before_or_equal:start_date',
            'location_type' => 'required|in:online,on_site,recorded',
            'levels' => 'nullable|array',
            'levels.*' => 'in:beginner,intermediate,advanced,all',
            'meeting_provider' => 'nullable|in:youtube,external',
            'online_link' => [
                'nullable',
                'url',
                Rule::requiredIf(function () use ($request) {
                    return $request->input('location_type') === 'online';
                }),
                function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                    if ($request->input('location_type') !== 'online') {
                        return;
                    }
                    if ($request->input('meeting_provider', 'youtube') !== 'youtube') {
                        return;
                    }
                    if (!YouTubeLive::isYouTubeUrl((string) $value)) {
                        $fail('رابط يوتيوب غير صالح. الصق رابط بث مباشر أو فيديو يوتيوب.');
                    }
                },
            ],
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
            'suitable_for_ar.*' => 'nullable|string|max:255',
            'suitable_for_en.*' => 'nullable|string|max:255',
            'buttons_text_ar.*' => 'nullable|string|max:100',
            'buttons_text_en.*' => 'nullable|string|max:100',
            'buttons_link.*' => 'nullable|url|max:500',
            'buttons_color.*' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/i',
            'buttons_needs_login.*' => 'nullable|in:0,1',

            // أيام الراحة - الجديد
            'rest_days' => 'nullable|array',
            'rest_days.*' => 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',

            'course_category_id' => 'required|exists:course_categories,id',
            'trainer_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'has_exam' => 'nullable|boolean',
            'exam_pass_score' => 'nullable|integer|min:1',
            'exam_duration_minutes' => 'nullable|integer|min:1|max:600',
            'required_exam_pass_count' => 'nullable|integer|min:1',
            'day_exams' => 'nullable|array',
            'day_exams.*.id' => 'nullable|integer',
            'day_exams.*.day_index' => 'nullable|integer|min:1',
            'day_exams.*.title' => 'nullable|string|max:255',
            'day_exams.*.pass_score' => 'nullable|integer|min:1',
            'day_exams.*.duration_minutes' => 'nullable|integer|min:1|max:600',
            'day_exams.*.questions' => 'nullable|array',
            'day_exams.*.questions.*.question' => 'nullable|string|max:1000',
            'day_exams.*.questions.*.answers' => 'nullable|array|min:1|max:6',
            'day_exams.*.questions.*.answers.*' => 'nullable|string|max:500',
            'day_exams.*.questions.*.correct' => 'nullable|integer|min:0|max:5',
            'exam_questions' => 'nullable|array',
            'exam_questions.*.question' => 'nullable|string|max:1000',
            'exam_questions.*.answers' => 'nullable|array|min:1|max:6',
            'exam_questions.*.answers.*' => 'nullable|string|max:500',
            'exam_questions.*.correct' => 'nullable|integer|min:0|max:5',
            'main_image' => ($id ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'video' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime,video/ogg|max:51200',
            'remove_video' => 'nullable|boolean',
            'units' => 'nullable|array',
            'units.*.items' => 'nullable|array',
            'units.*.items.*.video' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime,video/ogg,video/x-msvideo,video/avi|max:1048576',
            'units.*.items.*.thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'units.*.items.*.video_embed_url' => 'nullable|string|max:1000',
            'units.*.items.*.video_duration_seconds' => 'nullable|integer|min:0',
        ], [
            'price.max' => 'الحد الأقصى لسعر الدورة للمحاضر هو :max درهم.',
            'online_link.required' => 'رابط البث أو الاجتماع مطلوب للدورة الأونلاين',
            'online_link.url' => 'رابط الاجتماع غير صالح',
            'course_category_id.required' => 'اختر تصنيف الدورة',
            'requirements_ar.*.required' => 'كل متطلب بالعربية مطلوب',
            'features_ar.*.required' => 'كل ميزة بالعربية مطلوبة',
            'main_image.required' => 'الصورة الرئيسية مطلوبة عند الإضافة',
            'rest_days.*.in' => 'يوم الراحة المحدد غير صحيح',
            'video.max' => 'حجم الفيديو يجب ألا يتجاوز 50 ميجابايت',
            'video.mimetypes' => 'صيغة الفيديو غير مدعومة (MP4, WEBM, MOV, OGG)',
            'units.*.items.*.video.max' => 'حجم فيديو الدرس يجب ألا يتجاوز 1 جيجابايت',
            'units.*.items.*.video.mimetypes' => 'صيغة فيديو الدرس غير مدعومة (MP4, WEBM, MOV, OGG, AVI)',
            'units.*.items.*.thumbnail.image' => 'صورة المصغّر غير صالحة',
            'units.*.items.*.thumbnail.max' => 'حجم صورة المصغّر يجب ألا يتجاوز 4 ميجابايت',
        ]);

        unset($data['remove_video'], $data['meeting_provider']);

        // Courses are no longer tied to services
        $data['service_id'] = null;

        foreach (['description_ar', 'description_en', 'name_ar', 'name_en', 'venue_name', 'venue_details'] as $field) {
            if (array_key_exists($field, $data) && is_string($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        // Recorded courses have no live schedule — fill sentinel dates for non-null DB columns
        if (($data['location_type'] ?? null) === 'recorded') {
            $now = now();
            $farEnd = $now->copy()->addYears(10);
            // last_date must stay <= start_date (registration deadline semantics)
            $data['start_date'] = $now;
            $data['end_date'] = $farEnd;
            $data['last_date'] = $now;
            $data['count_days'] = 0;
            $data['rest_days'] = [];
            $data['online_link'] = null;
            $data['venue_name'] = null;
            $data['venue_map_url'] = null;
            $data['venue_details'] = null;
            $data['has_exam'] = false;
            $data['required_exam_pass_count'] = null;
            $data['exam_pass_score'] = null;
            $data['exam_duration_minutes'] = null;
        }

        $data['has_exam'] = $request->boolean('has_exam');
        if (($data['location_type'] ?? null) === 'recorded') {
            $data['has_exam'] = false;
        }
        if (!$data['has_exam'] && empty($request->input('day_exams'))) {
            $data['exam_pass_score'] = null;
            $data['exam_duration_minutes'] = null;
        }

        if (array_key_exists('required_exam_pass_count', $data) && $data['required_exam_pass_count'] !== null) {
            $data['required_exam_pass_count'] = max(1, (int) $data['required_exam_pass_count']);
        }

        return $data;
    }

    /**
     * Normalize YouTube Live URLs; leave external meeting links as submitted.
     */
    protected function applyMeetingProvider(Request $request, array &$data, ?Course $existing = null): void
    {
        if (($data['location_type'] ?? null) !== 'online') {
            return;
        }

        $submitted = trim((string) ($data['online_link'] ?? ''));
        if ($submitted === '') {
            return;
        }

        if ($request->input('meeting_provider', 'youtube') === 'youtube') {
            $watch = YouTubeLive::watchUrl($submitted);
            if ($watch) {
                $data['online_link'] = $watch;
            }
        }
    }

    protected function syncDayExams(Course $course, Request $request): void
    {
        if ($course->location_type === 'recorded') {
            $this->deleteDayExams($course);
            $course->update([
                'has_exam' => false,
                'required_exam_pass_count' => null,
                'exam_pass_score' => null,
                'exam_duration_minutes' => null,
            ]);
            return;
        }

        $locked = $course->dayExams()
            ->where(function ($q) {
                $q->whereNotNull('started_at')->orWhereNotNull('skipped_at');
            })
            ->exists();

        if ($locked) {
            return;
        }

        if (!$request->boolean('has_exam')) {
            $this->deleteDayExams($course);
            $course->update([
                'has_exam' => false,
                'required_exam_pass_count' => null,
                'exam_pass_score' => null,
                'exam_duration_minutes' => null,
            ]);
            return;
        }

        $dayExamsInput = $request->input('day_exams', []);
        if (!is_array($dayExamsInput)) {
            $dayExamsInput = [];
        }

        $teachingDays = max(1, $course->teachingDays()->count() ?: (int) ($course->count_days ?: 1));
        $cleaned = [];

        foreach ($dayExamsInput as $eIndex => $examData) {
            if (!is_array($examData)) {
                continue;
            }

            $dayIndex = max(1, (int) ($examData['day_index'] ?? 1));
            if ($dayIndex > $teachingDays) {
                continue;
            }

            $questionsInput = $examData['questions'] ?? [];
            $cleanedQuestions = [];

            foreach ($questionsInput as $qIndex => $qData) {
                if (!is_array($qData)) {
                    continue;
                }
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
                        "day_exams.{$eIndex}.questions.{$qIndex}.question" => 'كل سؤال يحتاج نصاً ومن 1 إلى 6 إجابات.',
                    ]);
                }

                if ($correct < 0 || $correct >= count($answers)) {
                    throw ValidationException::withMessages([
                        "day_exams.{$eIndex}.questions.{$qIndex}.correct" => 'يجب تحديد الإجابة الصحيحة لكل سؤال.',
                    ]);
                }

                $cleanedQuestions[] = [
                    'question' => $text,
                    'answers' => $answers,
                    'correct' => $correct,
                ];
            }

            if (empty($cleanedQuestions)) {
                continue;
            }

            $passScore = (int) ($examData['pass_score'] ?? 1);
            if ($passScore < 1 || $passScore > count($cleanedQuestions)) {
                throw ValidationException::withMessages([
                    "day_exams.{$eIndex}.pass_score" => 'درجة النجاح يجب أن تكون بين 1 وعدد الأسئلة.',
                ]);
            }

            $duration = (int) ($examData['duration_minutes'] ?? 0);
            if ($duration < 1) {
                throw ValidationException::withMessages([
                    "day_exams.{$eIndex}.duration_minutes" => 'يجب تحديد مدة الاختبار بالدقائق (دقيقة واحدة على الأقل).',
                ]);
            }

            $cleaned[] = [
                'day_index' => $dayIndex,
                'title' => trim((string) ($examData['title'] ?? '')) ?: null,
                'pass_score' => $passScore,
                'duration_minutes' => $duration,
                'questions' => $cleanedQuestions,
            ];
        }

        usort($cleaned, function ($a, $b) {
            return [$a['day_index'], $a['title'] ?? ''] <=> [$b['day_index'], $b['title'] ?? ''];
        });

        $examCount = count($cleaned);
        $required = (int) $request->input('required_exam_pass_count', $examCount > 0 ? 1 : null);

        if ($examCount > 0) {
            if ($required < 1 || $required > $examCount) {
                throw ValidationException::withMessages([
                    'required_exam_pass_count' => 'عدد الاختبارات المطلوب اجتيازها يجب أن يكون بين 1 وعدد الاختبارات.',
                ]);
            }
        } else {
            $required = null;
        }

        DB::transaction(function () use ($course, $cleaned, $required, $examCount) {
            $this->deleteDayExams($course);

            $sort = 0;
            foreach ($cleaned as $examData) {
                $dayExam = $course->dayExams()->create([
                    'day_index' => $examData['day_index'],
                    'sort_order' => $sort++,
                    'title' => $examData['title'],
                    'pass_score' => $examData['pass_score'],
                    'duration_minutes' => $examData['duration_minutes'],
                ]);

                foreach ($examData['questions'] as $qi => $qData) {
                    $question = $dayExam->questions()->create([
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
            }

            $course->update([
                'has_exam' => $examCount > 0,
                'required_exam_pass_count' => $required,
                'exam_pass_score' => null,
                'exam_duration_minutes' => null,
            ]);
        });
    }

    protected function deleteDayExams(Course $course): void
    {
        $course->loadMissing('dayExams.questions.answers');
        foreach ($course->dayExams as $exam) {
            foreach ($exam->questions as $question) {
                $question->answers()->delete();
                $question->delete();
            }
            $exam->attempts()->delete();
            $exam->delete();
        }
    }

    public function startDayExam(Course $course, CourseDayExam $dayExam)
    {
        $this->authorizeCourseManager($course);
        abort_unless((int) $dayExam->course_id === (int) $course->id, 404);
        abort_if($course->isRecorded(), 404);

        if (!$course->canStartDayExam($dayExam)) {
            return back()->with('error', 'لا يمكن بدء هذا الاختبار الآن. تأكد من إنهاء أو تخطي الاختبار السابق أولاً.');
        }

        $dayExam->update([
            'started_at' => now(),
            'ended_at' => null,
            'skipped_at' => null,
            'skipped_by' => null,
        ]);

        return back()->with('success', 'تم بدء الاختبار. سيتم تحويل الحضور تلقائياً لصفحة الاختبار.');
    }

    public function endDayExam(Course $course, CourseDayExam $dayExam)
    {
        $this->authorizeCourseManager($course);
        abort_unless((int) $dayExam->course_id === (int) $course->id, 404);
        abort_if($course->isRecorded(), 404);

        if (!$dayExam->isRunning()) {
            return back()->with('error', 'هذا الاختبار غير جارٍ حالياً.');
        }

        $dayExam->update(['ended_at' => now()]);

        return back()->with('success', 'تم إنهاء الاختبار.');
    }

    public function skipDayExam(Course $course, CourseDayExam $dayExam)
    {
        $this->authorizeCourseManager($course);
        abort_unless((int) $dayExam->course_id === (int) $course->id, 404);
        abort_if($course->isRecorded(), 404);

        if (!$course->canSkipDayExam($dayExam)) {
            return back()->with('error', 'لا يمكن تخطي هذا الاختبار الآن.');
        }

        $dayExam->update([
            'skipped_at' => now(),
            'skipped_by' => auth()->id(),
            'started_at' => null,
            'ended_at' => null,
        ]);

        return back()->with('success', 'تم تخطي الاختبار.');
    }

    /**
     * @deprecated kept for old bookmarks — redirects to first pending/running day exam actions via flash.
     */
    public function startExam(Course $course)
    {
        $this->authorizeCourseManager($course);
        $exam = $course->dayExams->first(fn (CourseDayExam $e) => $course->canStartDayExam($e));
        if (!$exam) {
            return back()->with('error', 'لا يوجد اختبار جاهز للبدء.');
        }

        return $this->startDayExam($course, $exam);
    }

    public function endExam(Course $course)
    {
        $this->authorizeCourseManager($course);
        $running = $course->runningDayExam();
        if (!$running) {
            return back()->with('error', 'لا يوجد اختبار جارٍ.');
        }

        return $this->endDayExam($course, $running);
    }

    /**
     * Live exam progress for admin/trainer subscribers table.
     */
    public function examStatuses(Course $course)
    {
        $this->authorizeCourseManager($course);

        if (!$course->usesDayExams()) {
            return response()->json(['statuses' => []]);
        }

        $course->load(['dayExams', 'dayExamAttempts', 'ratings']);
        $running = $course->runningDayExam();
        $runningAttempts = $running
            ? $running->attempts()->get()->keyBy('user_id')
            : collect();
        $totalQuestions = $running ? $running->questions()->count() : 0;

        $userIds = $course->payments()
            ->whereIn('status', ['completed', 'success', 'paid', 'active', 'pending'])
            ->where('is_attended', true)
            ->pluck('user_id')
            ->unique();

        $statuses = [];
        foreach ($userIds as $userId) {
            $attempt = $running ? $runningAttempts->get($userId) : null;
            $status = $course->userExamStatus($userId, $attempt);
            $statuses[(string) $userId] = [
                'status' => $status,
                'label' => $course->userExamStatusLabel($userId, $attempt),
                'score' => $attempt && $attempt->isSubmitted() ? (int) $attempt->score : null,
                'total' => $totalQuestions,
                'passed' => $attempt && $attempt->isSubmitted() ? (bool) $attempt->passed : null,
                'passed_count' => $course->userPassedDayExamCount($userId),
                'required_pass' => $course->effectiveRequiredExamPassCount(),
                'rating_done' => $course->userCompletedRating($userId),
                'can_certificate' => $course->userCanGetCertificate($userId),
            ];
        }

        return response()->json([
            'statuses' => $statuses,
            'running_exam_id' => $running?->id,
            'all_finished' => $course->areAllDayExamsFinished(),
        ]);
    }

    public function payments(Course $course)
    {
        $payments = $course->students()->get();
        return view('dashboard.courses.payments', compact('course', 'payments'));
    }

    public function userShow(Course $course)
    {
        $course->loadCount(['payments' => function ($query) {
            $query->whereIn('status', ['completed', 'success', 'paid']);
        }]);
        $course->total_participants = ($course->payments_count ?? 0) + ($course->counter ?? 0);

        $course->load([
            'category',
            'featuredRatings.user',
            'completedRatings',
            'units.items',
        ]);

        $related_courses = Course::query()
            ->when($course->course_category_id, fn ($q) => $q->where('course_category_id', $course->course_category_id))
            ->when(!$course->course_category_id, fn ($q) => $q->whereRaw('1 = 0'))
            ->where('id', '!=', $course->id)
            ->where('status', 'active')
            ->with('category')
            ->withCount(['payments' => function ($query) {
                $query->whereIn('status', ['completed', 'success', 'paid']);
            }])
            ->limit(6)
            ->get()
            ->each(function ($item) {
                $item->total_participants = max(0, ($item->counter ?? 0) - ($item->payments_count ?? 0));
            });

        $this->decorateAcademyCourseCards($related_courses);

        $is_enrolled = $course->isUserEnrolled();
        $course->academy_wishlisted = Auth::check()
            && CourseWishlist::query()
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->exists();
        // Average from all completed trainee ratings (not only featured)
        $featuredAverage = $course->averageRatingScore();
        $featuredCount = $course->completedRatingsCount();
        $lessonCount = $course->isRecorded() ? $course->pathLessonCount() : 0;
        $examCount = $course->isRecorded() ? $course->pathExamCount() : 0;
        $contentDurationSeconds = $course->isRecorded() ? $course->totalContentDurationSeconds() : 0;

        return view('course.show', compact(
            'course',
            'is_enrolled',
            'related_courses',
            'featuredAverage',
            'featuredCount',
            'lessonCount',
            'examCount',
            'contentDurationSeconds'
        ));
    }

    public function toggleFeaturedRating(Course $course, CourseRating $rating)
    {
        $this->authorizeCourseManager($course);
        abort_unless((int) $rating->course_id === (int) $course->id, 404);
        abort_unless($rating->isCompleted(), 422);

        $rating->update(['is_featured' => !$rating->is_featured]);

        return back()->with(
            'success',
            $rating->is_featured
                ? 'تم إظهار التقييم في صفحة الدورة العامة'
                : 'تم إخفاء التقييم من صفحة الدورة العامة'
        );
    }

    /**
     * Resolve video duration (seconds) for an embed/external lesson URL.
     */
    public function resolveVideoDuration(Request $request)
    {
        $this->authorizeCourseManageList();

        $data = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
        ]);

        $seconds = LessonVideoSource::resolveDurationSeconds($data['url']);

        return response()->json([
            'seconds' => $seconds,
        ]);
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
        $payment = Payment::with('course')->findOrFail($paymentId);
        $this->authorizeCourseManager($payment->course);

        $payment->is_attended = !$payment->is_attended;
        $payment->save();

        return back()->with('success', 'تم تحديث حالة الحضور بنجاح');
    }

    public function bulkAttendance(Request $request, Course $course)
    {
        $this->authorizeCourseManager($course);

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
        $payment = Payment::with(['user', 'course.dayExams', 'course.ratings'])->findOrFail($paymentId);
        $course = $payment->course;

        $isEnrolled = in_array((string) $payment->status, ['completed', 'success', 'paid', 'active', 'pending'], true);
        if (!$isEnrolled) {
            return redirect()->route('dashboard.my_courses.index')
                ->with('error', 'لا يمكن استخراج شهادة لهذا الاشتراك');
        }

        // Avoid redirect loops: never use back() between rating ↔ certificate.
        if ($course && !$course->userCanGetCertificate($payment->user_id)) {
            if ($course->userNeedsRating($payment->user_id)) {
                return redirect()->route('dashboard.courses.rating', $course)
                    ->with('error', 'يجب إكمال تقييم الدورة قبل استخراج الشهادة');
            }

            if ($course->usesDayExams()) {
                if (!$course->areAllDayExamsFinished()) {
                    return redirect()->route('dashboard.my_courses.index')
                        ->with('error', 'الشهادة متاحة بعد انتهاء جميع اختبارات الدورة');
                }
                if (!$course->userMetExamPassRequirement($payment->user_id)) {
                    return redirect()->route('dashboard.my_courses.index')
                        ->with('error', 'الشهادة متاحة فقط بعد اجتياز العدد المطلوب من الاختبارات');
                }
            }

            return redirect()->route('dashboard.my_courses.index')
                ->with('error', 'الشهادة غير متاحة حالياً');
        }

        // Rich HTML preview (same design as academy trust certificate).
        if (request()->boolean('html')) {
            return response()
                ->view('dashboard.courses.certificate', compact('payment'))
                ->header('Content-Disposition', 'inline; filename="certificate.html"');
        }

        $safeName = preg_replace('/[^\p{L}\p{N}\-_]+/u', '-', (string) $payment->user->name) ?: 'certificate';
        $filename = 'certificate-'.$safeName.'.pdf';

        $pdf = Pdf::loadView('dashboard.courses.certificate-pdf', compact('payment'))
            ->setPaper('a4', 'portrait');

        // Opens in the browser PDF viewer; user can print from there.
        return $pdf->stream($filename);
    }

    /**
     * Sync recorded-course educational path (units → lessons/exams).
     */
    protected function syncEducationalPath(Course $course, Request $request): void
    {
        if ($course->location_type !== 'recorded') {
            // Switching away from recorded: wipe path + videos only when a path exists
            if ($course->units()->exists()) {
                $this->deleteEducationalPath($course);
                $course->update(['total_video_duration_seconds' => 0]);
            }
            return;
        }

        $unitsInput = $request->input('units', []);
        if (!is_array($unitsInput)) {
            $unitsInput = [];
        }

        $keptUnitIds = [];
        $keptItemIds = [];
        $totalDuration = 0;
        $unitOrder = 0;

        foreach ($unitsInput as $uIndex => $unitData) {
            if (!is_array($unitData)) {
                continue;
            }

            $itemsInput = $unitData['items'] ?? [];
            if (!is_array($itemsInput)) {
                $itemsInput = [];
            }

            $titleAr = trim((string) ($unitData['title_ar'] ?? $unitData['title'] ?? ''));
            $titleEn = trim((string) ($unitData['title_en'] ?? ''));

            // Keep units that have at least one named lesson/exam even if the unit title was left blank
            $namedItems = collect($itemsInput)->filter(function ($itemData) {
                if (!is_array($itemData)) {
                    return false;
                }
                $ar = trim((string) ($itemData['title_ar'] ?? $itemData['title'] ?? ''));
                $en = trim((string) ($itemData['title_en'] ?? ''));

                return $ar !== '' || $en !== '';
            });

            if ($titleAr === '' && $titleEn === '' && $namedItems->isEmpty()) {
                continue;
            }

            if ($titleAr === '' && $titleEn === '') {
                $firstItem = $namedItems->first() ?: [];
                $fallback = trim((string) (
                    ($firstItem['title_ar'] ?? '')
                    ?: ($firstItem['title_en'] ?? '')
                    ?: ($firstItem['title'] ?? '')
                ));
                $titleAr = $fallback !== '' ? $fallback : ('وحدة ' . ($unitOrder + 1));
                $titleEn = $fallback !== '' ? $fallback : ('Unit ' . ($unitOrder + 1));
            }
            if ($titleAr === '') {
                $titleAr = $titleEn;
            }
            if ($titleEn === '') {
                $titleEn = $titleAr;
            }

            $unitId = !empty($unitData['id']) ? (int) $unitData['id'] : null;
            $unit = $unitId
                ? CourseUnit::where('course_id', $course->id)->where('id', $unitId)->first()
                : null;

            if ($unit) {
                $unit->update([
                    'title_ar' => $titleAr,
                    'title_en' => $titleEn,
                    'sort_order' => $unitOrder,
                ]);
            } else {
                $unit = CourseUnit::create([
                    'course_id' => $course->id,
                    'title_ar' => $titleAr,
                    'title_en' => $titleEn,
                    'sort_order' => $unitOrder,
                ]);
            }
            $keptUnitIds[] = $unit->id;
            $unitOrder++;

            $itemOrder = 0;
            $unitKeptItemIds = [];
            foreach ($itemsInput as $iIndex => $itemData) {
                $itemTitleAr = trim((string) ($itemData['title_ar'] ?? $itemData['title'] ?? ''));
                $itemTitleEn = trim((string) ($itemData['title_en'] ?? ''));
                $type = ($itemData['type'] ?? 'lesson') === 'exam' ? 'exam' : 'lesson';
                if ($itemTitleAr === '' && $itemTitleEn === '') {
                    continue;
                }
                if ($itemTitleAr === '') {
                    $itemTitleAr = $itemTitleEn;
                }
                if ($itemTitleEn === '') {
                    $itemTitleEn = $itemTitleAr;
                }

                $itemId = !empty($itemData['id']) ? (int) $itemData['id'] : null;
                $item = $itemId
                    ? CoursePathItem::where('unit_id', $unit->id)->where('id', $itemId)->first()
                    : null;

                $payload = [
                    'unit_id' => $unit->id,
                    'type' => $type,
                    'title_ar' => $itemTitleAr,
                    'title_en' => $itemTitleEn,
                    'sort_order' => $itemOrder,
                    'exam_pass_score' => $type === 'exam' ? (int) ($itemData['exam_pass_score'] ?? 1) : null,
                    'exam_duration_minutes' => $type === 'exam' ? (int) ($itemData['exam_duration_minutes'] ?? 30) : null,
                ];

                $fileKey = "units.{$uIndex}.items.{$iIndex}.video";
                $embedUrl = trim((string) ($itemData['video_embed_url'] ?? ''));
                $videoSource = ($itemData['video_source'] ?? '') === 'embed' ? 'embed' : 'upload';

                if ($embedUrl !== '' && !filter_var($embedUrl, FILTER_VALIDATE_URL)) {
                    throw ValidationException::withMessages([
                        "units.{$uIndex}.items.{$iIndex}.video_embed_url" => 'رابط الفيديو غير صالح.',
                    ]);
                }

                if ($type === 'lesson') {
                    $payload['video_duration_seconds'] = max(
                        0,
                        (int) ($itemData['video_duration_seconds'] ?? $item?->video_duration_seconds ?? 0)
                    );

                    $thumbKey = "units.{$uIndex}.items.{$iIndex}.thumbnail";
                    if ($request->hasFile($thumbKey)) {
                        if ($item?->video_thumbnail_path) {
                            Storage::disk('public')->delete($item->video_thumbnail_path);
                        }
                        $payload['video_thumbnail_path'] = WatermarkedUpload::store(
                            $request->file($thumbKey),
                            'courses/path-thumbnails'
                        );
                    } elseif (!empty($itemData['remove_thumbnail']) && $item?->video_thumbnail_path) {
                        Storage::disk('public')->delete($item->video_thumbnail_path);
                        $payload['video_thumbnail_path'] = null;
                    }

                    if ($videoSource === 'embed') {
                        if ($embedUrl === '' && empty($item?->video_embed_url)) {
                            // allow empty while drafting
                            $payload['video_embed_url'] = null;
                        } else {
                            $payload['video_embed_url'] = $embedUrl !== '' ? $embedUrl : $item?->video_embed_url;
                        }
                        if ($item?->video_path) {
                            \App\Support\VideoDownloadGuard::deleteStored($item->video_path);
                        }
                        $payload['video_path'] = null;
                    } elseif ($request->hasFile($fileKey)) {
                        if ($item?->video_path) {
                            \App\Support\VideoDownloadGuard::deleteStored($item->video_path);
                        }
                        $payload['video_path'] = WatermarkedUpload::store(
                            $request->file($fileKey),
                            'courses/path-videos',
                            \App\Support\VideoDownloadGuard::storageDisk()
                        );
                        $payload['video_embed_url'] = null;
                        $payload['video_duration_seconds'] = max(0, (int) ($itemData['video_duration_seconds'] ?? 0));
                    } else {
                        // Stay on upload mode: keep existing file, drop embed
                        $payload['video_embed_url'] = null;
                        if (!empty($itemData['remove_video']) && $item?->video_path) {
                            \App\Support\VideoDownloadGuard::deleteStored($item->video_path);
                            $payload['video_path'] = null;
                            $payload['video_duration_seconds'] = 0;
                        }
                    }
                } else {
                    if ($item?->video_path) {
                        \App\Support\VideoDownloadGuard::deleteStored($item->video_path);
                    }
                    if ($item?->video_thumbnail_path) {
                        Storage::disk('public')->delete($item->video_thumbnail_path);
                    }
                    $payload['video_path'] = null;
                    $payload['video_thumbnail_path'] = null;
                    $payload['video_embed_url'] = null;
                    $payload['video_duration_seconds'] = null;
                }

                if ($item) {
                    $item->update($payload);
                } else {
                    $item = CoursePathItem::create($payload);
                }
                $unitKeptItemIds[] = $item->id;
                $keptItemIds[] = $item->id;
                $itemOrder++;

                if ($type === 'lesson') {
                    $totalDuration += (int) ($item->fresh()->video_duration_seconds ?? $payload['video_duration_seconds'] ?? 0);
                }

                if ($type === 'exam') {
                    $this->syncPathItemExamQuestions($item, $itemData['questions'] ?? []);
                } else {
                    $item->examQuestions()->each(function (CoursePathExamQuestion $q) {
                        $q->answers()->delete();
                        $q->delete();
                    });
                }
            }

            // Delete removed items in this unit
            $unit->items()->whereNotIn('id', $unitKeptItemIds ?: [0])->each(function (CoursePathItem $old) {
                $this->deletePathItemMedia($old);
                $old->examQuestions()->each(function (CoursePathExamQuestion $q) {
                    $q->answers()->delete();
                    $q->delete();
                });
                $old->delete();
            });
        }

        // Delete removed units
        $course->units()->whereNotIn('id', $keptUnitIds ?: [0])->each(function (CourseUnit $unit) {
            $unit->items->each(function (CoursePathItem $old) {
                $this->deletePathItemMedia($old);
                $old->examQuestions()->each(function (CoursePathExamQuestion $q) {
                    $q->answers()->delete();
                    $q->delete();
                });
                $old->delete();
            });
            $unit->delete();
        });

        $course->update(['total_video_duration_seconds' => $totalDuration]);
    }

    protected function syncPathItemExamQuestions(CoursePathItem $item, array $questions): void
    {
        $item->examQuestions()->each(function (CoursePathExamQuestion $q) {
            $q->answers()->delete();
            $q->delete();
        });

        $qOrder = 0;
        foreach ($questions as $qData) {
            $text = trim((string) ($qData['question'] ?? ''));
            $answers = array_values(array_filter(
                array_map(fn ($a) => trim((string) $a), $qData['answers'] ?? []),
                fn ($a) => $a !== ''
            ));
            $correct = isset($qData['correct']) ? (int) $qData['correct'] : 0;
            if ($text === '' || empty($answers)) {
                continue;
            }

            $question = CoursePathExamQuestion::create([
                'path_item_id' => $item->id,
                'question' => $text,
                'sort_order' => $qOrder++,
            ]);

            foreach ($answers as $ai => $answerText) {
                CoursePathExamAnswer::create([
                    'question_id' => $question->id,
                    'answer' => $answerText,
                    'is_correct' => $ai === $correct,
                    'sort_order' => $ai,
                ]);
            }
        }
    }

    protected function deleteEducationalPath(Course $course): void
    {
        $course->load('units.items.examQuestions');
        foreach ($course->units as $unit) {
            foreach ($unit->items as $item) {
                $this->deletePathItemMedia($item);
                foreach ($item->examQuestions as $q) {
                    $q->answers()->delete();
                    $q->delete();
                }
                $item->delete();
            }
            $unit->delete();
        }
    }

    protected function deletePathItemMedia(CoursePathItem $item): void
    {
        if ($item->video_path) {
            \App\Support\VideoDownloadGuard::deleteStored($item->video_path);
        }
        if ($item->video_thumbnail_path) {
            Storage::disk('public')->delete($item->video_thumbnail_path);
        }
    }

    /**
     * Attach rating / ownership fields used by academy course cards.
     *
     * @param  Collection<int, Course>  $courses
     */
    protected function decorateAcademyCourseCards(Collection $courses): void
    {
        if ($courses->isEmpty()) {
            return;
        }

        $ids = $courses->pluck('id')->unique()->filter()->values();
        $grouped = CourseRating::query()
            ->whereIn('course_id', $ids)
            ->whereNotNull('completed_at')
            ->get(['course_id', 'answers'])
            ->groupBy('course_id');

        foreach ($courses as $course) {
            $ratings = $grouped->get($course->id, collect());
            $scores = $ratings
                ->map(fn (CourseRating $r) => $r->averageScaleScore())
                ->filter(fn ($s) => $s !== null);

            $course->academy_avg_rating = $scores->isNotEmpty() ? round($scores->avg(), 1) : null;
            $course->academy_owned = false;
            $course->academy_payment = null;
            $course->academy_path_percent = 0;
            $course->academy_wishlisted = false;
        }

        $userId = Auth::id();
        if (! $userId) {
            return;
        }

        $wishlisted = CourseWishlist::query()
            ->where('user_id', $userId)
            ->whereIn('course_id', $ids)
            ->pluck('course_id')
            ->flip();

        foreach ($courses as $course) {
            $course->academy_wishlisted = $wishlisted->has($course->id);
        }

        $payments = Payment::query()
            ->where('user_id', $userId)
            ->whereIn('course_id', $ids)
            ->whereIn('status', ['completed', 'success', 'paid', 'active'])
            ->latest()
            ->get()
            ->groupBy('course_id');

        foreach ($courses as $course) {
            $payment = $payments->get($course->id)?->first();
            if (! $payment) {
                continue;
            }

            $course->academy_owned = true;
            $course->academy_payment = $payment;
            if ($course->isRecorded()) {
                $course->academy_path_percent = (int) ($course->pathCompletionForUser($userId)['percent'] ?? 0);
            }
        }
    }
}
