<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseDayExam;
use App\Models\CourseDayExamAnswer;
use App\Models\CourseDayExamQuestion;
use App\Models\CoursePathExamAnswer;
use App\Models\CoursePathExamQuestion;
use App\Models\CoursePathItem;
use App\Models\CourseRating;
use App\Models\CourseUnit;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Additive academy demo data — never deletes or overwrites existing records.
 * Safe to re-run: uses firstOrCreate / updateOrCreate on stable seed keys.
 *
 *   php artisan db:seed --class=AcademySeeder
 */
class AcademySeeder extends Seeder
{
    public function run(): void
    {
        $categories = $this->ensureCategories();
        $fallbackImage = Course::query()->whereNotNull('main_image')->value('main_image')
            ?: 'courses/main/placeholder.jpg';
        $fallbackAvatar = User::query()->where('role', 'trainer')->whereNotNull('avatar')->value('avatar');

        $trainers = $this->seedTrainers($categories, $fallbackAvatar);
        $courses = $this->seedCourses($categories, $trainers, $fallbackImage);
        $trainees = $this->seedTrainees();
        $this->seedReviews($courses, $trainees);

        $this->command?->info(sprintf(
            'Academy seed done. Categories: %d | Active trainers: %d | Seed courses: %d | Day exams: %d | Path units: %d | Ratings: %d',
            $categories->count(),
            User::where('role', 'trainer')->where('status', 'active')->count(),
            Course::where('name_en', 'like', 'SEED-%')->count(),
            CourseDayExam::whereHas('course', fn ($q) => $q->where('name_en', 'like', 'SEED-%'))->count(),
            CourseUnit::whereHas('course', fn ($q) => $q->where('name_en', 'like', 'SEED-%'))->count(),
            CourseRating::whereHas('user', fn ($q) => $q->where('email', 'like', 'seed.trainee%@academy.test'))->count()
        ));
    }

    /**
     * Keep existing categories; only create missing common ones.
     */
    protected function ensureCategories()
    {
        $defaults = [
            ['title_ar' => 'الموشن جرافيك', 'title_en' => 'Motion Graphic', 'sort_order' => 1],
            ['title_ar' => 'التصميم', 'title_en' => 'Design', 'sort_order' => 2],
            ['title_ar' => 'صناعة المحتوي', 'title_en' => 'Content Creation', 'sort_order' => 3],
            ['title_ar' => 'مونتاج الفيديو', 'title_en' => 'Video Editing', 'sort_order' => 4],
            ['title_ar' => 'الثري دي', 'title_en' => '3D', 'sort_order' => 5],
            ['title_ar' => 'تطوير المواقع و التطبيقات', 'title_en' => 'Website And Application Development', 'sort_order' => 6],
            ['title_ar' => 'الذكاء الإصطناعي', 'title_en' => 'Artificial Intelligence', 'sort_order' => 7],
            ['title_ar' => 'التسويق الرقمي', 'title_en' => 'Digital Marketing', 'sort_order' => 8],
            ['title_ar' => 'إدارة الأعمال', 'title_en' => 'Business Management', 'sort_order' => 9],
        ];

        foreach ($defaults as $row) {
            CourseCategory::firstOrCreate(
                ['title_en' => $row['title_en']],
                [
                    'title_ar' => $row['title_ar'],
                    'description_ar' => 'قسم ' . $row['title_ar'],
                    'description_en' => $row['title_en'] . ' category',
                    'sort_order' => $row['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        return CourseCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    protected function seedTrainers($categories, ?string $fallbackAvatar): array
    {
        $profiles = [
            ['name' => 'سارة المنصوري', 'email' => 'seed.trainer.sara@academy.test', 'country' => 'AE', 'phone' => '+971501110001'],
            ['name' => 'أحمد الخطيب', 'email' => 'seed.trainer.ahmed@academy.test', 'country' => 'JO', 'phone' => '+962791110002'],
            ['name' => 'نورة العتيبي', 'email' => 'seed.trainer.noura@academy.test', 'country' => 'SA', 'phone' => '+966501110003'],
            ['name' => 'كريم حسن', 'email' => 'seed.trainer.karim@academy.test', 'country' => 'EG', 'phone' => '+20101110004'],
            ['name' => 'ليلى فرحات', 'email' => 'seed.trainer.layla@academy.test', 'country' => 'MA', 'phone' => '+21261110005'],
            ['name' => 'يوسف الراشد', 'email' => 'seed.trainer.yousef@academy.test', 'country' => 'KW', 'phone' => '+96550111006'],
            ['name' => 'مريم الشامسي', 'email' => 'seed.trainer.mariam@academy.test', 'country' => 'AE', 'phone' => '+971501110007'],
            ['name' => 'عمر ناجي', 'email' => 'seed.trainer.omar@academy.test', 'country' => 'LB', 'phone' => '+96171110008'],
        ];

        $trainers = [];
        $catCount = max(1, $categories->count());

        foreach ($profiles as $i => $profile) {
            $category = $categories[$i % $catCount];

            $trainer = User::firstOrCreate(
                ['email' => $profile['email']],
                [
                    'name' => $profile['name'],
                    'account_type' => 'personal',
                    'password' => Hash::make('password'),
                    'phone' => $profile['phone'],
                    'country' => $profile['country'],
                    'role' => 'trainer',
                    'status' => 'active',
                    'course_category_id' => $category->id,
                    'avatar' => $fallbackAvatar,
                    'email_verified_at' => now(),
                    'terms_accepted_at' => now(),
                    'whatsapp_verified' => true,
                ]
            );

            // Ensure approved + category without overwriting unrelated existing accounts
            if ($trainer->wasRecentlyCreated || Str::startsWith($trainer->email, 'seed.trainer.')) {
                $trainer->forceFill([
                    'role' => 'trainer',
                    'status' => 'active',
                    'country' => $profile['country'],
                    'course_category_id' => $category->id,
                    'avatar' => $trainer->avatar ?: $fallbackAvatar,
                ])->save();
            }

            $trainers[] = $trainer;
        }

        return $trainers;
    }

    protected function seedCourses($categories, array $trainers, string $fallbackImage): array
    {
        $existingImages = Course::query()
            ->whereNotNull('main_image')
            ->pluck('main_image')
            ->filter()
            ->values()
            ->all();

        if (empty($existingImages)) {
            $existingImages = [$fallbackImage];
        }

        $courses = [];
        $trainerCount = max(1, count($trainers));
        $imageIndex = 0;
        $courseIndex = 0;

        // Per category: online/on_site × (1 day + multi-day) + recorded with full path
        $liveVariants = [
            ['type' => 'online', 'days' => 1, 'label_ar' => 'أونلاين — يوم واحد', 'label_en' => 'Online 1-Day', 'price' => 199],
            ['type' => 'online', 'days' => 3, 'label_ar' => 'أونلاين — 3 أيام', 'label_en' => 'Online 3-Day', 'price' => 449],
            ['type' => 'on_site', 'days' => 1, 'label_ar' => 'حضوري — يوم واحد', 'label_en' => 'On-site 1-Day', 'price' => 299],
            ['type' => 'on_site', 'days' => 3, 'label_ar' => 'حضوري — 3 أيام', 'label_en' => 'On-site 3-Day', 'price' => 650],
        ];

        foreach ($categories as $category) {
            foreach ($liveVariants as $variant) {
                $slug = Str::upper(Str::slug($category->title_en));
                $seedKey = sprintf('SEED-%s-%s-%dD', $slug, Str::upper($variant['type'] === 'on_site' ? 'ONSITE' : 'ONLINE'), $variant['days']);
                $trainer = $trainers[$courseIndex % $trainerCount];
                $image = $existingImages[$imageIndex % count($existingImages)];
                $imageIndex++;
                $courseIndex++;

                $course = $this->upsertSeedCourse([
                    'seed_key' => $seedKey,
                    'name_ar' => sprintf('دورة %s — %s', $category->title_ar, $variant['label_ar']),
                    'type' => $variant['type'],
                    'days' => $variant['days'],
                    'price' => $variant['price'],
                    'category' => $category,
                    'trainer' => $trainer,
                    'image' => $image,
                ]);

                $this->seedDayExamsForCourse($course, $variant['days']);
                $courses[] = $course;
            }

            // Recorded course with units / lessons / exams
            $slug = Str::upper(Str::slug($category->title_en));
            $seedKey = sprintf('SEED-%s-RECORDED', $slug);
            $trainer = $trainers[$courseIndex % $trainerCount];
            $image = $existingImages[$imageIndex % count($existingImages)];
            $imageIndex++;
            $courseIndex++;

            $course = $this->upsertSeedCourse([
                'seed_key' => $seedKey,
                'name_ar' => sprintf('دورة %s — مسجّلة', $category->title_ar),
                'type' => 'recorded',
                'days' => 0,
                'price' => 149,
                'category' => $category,
                'trainer' => $trainer,
                'image' => $image,
            ]);

            $this->seedRecordedPath($course);
            $courses[] = $course;
        }

        // Enrich any older simple SEED-{CAT}-{TYPE} rows created by previous seeder runs
        foreach (Course::where('name_en', 'like', 'SEED-%')->where('name_en', 'not like', 'SEED-%-%D')->where('name_en', 'not like', 'SEED-%-RECORDED')->get() as $legacy) {
            if ($legacy->isRecorded()) {
                $this->seedRecordedPath($legacy);
            } elseif (in_array($legacy->location_type, ['online', 'on_site'], true)) {
                $days = max(1, (int) ($legacy->count_days ?: 1));
                $this->seedDayExamsForCourse($legacy, $days);
            }
        }

        return $courses;
    }

    protected function upsertSeedCourse(array $data): Course
    {
        $type = $data['type'];
        $days = (int) $data['days'];
        $category = $data['category'];
        $trainer = $data['trainer'];
        $seedKey = $data['seed_key'];

        $start = now()->subDays(1)->setTime(10, 0);
        $end = $type === 'recorded'
            ? now()->addYears(5)
            : $start->copy()->addDays(max(1, $days))->setTime(18, 0);

        $attrs = [
            'name_ar' => $data['name_ar'],
            'location_type' => $type,
            'levels' => ['beginner', 'intermediate'],
            'online_link' => $type === 'online' ? 'https://www.youtube.com/watch?v=aqz-KE-bpKQ' : null,
            'venue_name' => $type === 'on_site' ? 'قاعة الأكاديمية — دبي' : null,
            'venue_map_url' => $type === 'on_site' ? 'https://maps.google.com/?q=Dubai' : null,
            'venue_details' => $type === 'on_site' ? 'الطابق الثاني، قاعة التدريب الرئيسية، مواقف متاحة' : null,
            'counter' => 40,
            'count_days' => $type === 'recorded' ? 0 : $days,
            'course_category_id' => $category->id,
            'price' => $data['price'],
            'description_ar' => sprintf(
                "دورة تدريبية شاملة في مجال %s.\n\nتشمل هذه الدورة محتوى نظري وتطبيقي، مع تقييم يومي وشهادة بعد إكمال التقييم.\nالمستوى: مبتدئ إلى متوسط.\nمدة الدورة: %s.",
                $category->title_ar,
                $type === 'recorded' ? 'تعلم ذاتي حسب وتيرتك' : ($days . ' يوم/أيام')
            ),
            'description_en' => sprintf(
                "A complete training course in %s.\n\nIncludes practical content, daily assessment, and a certificate after completing the rating.\nLevel: beginner to intermediate.\nDuration: %s.",
                $category->title_en,
                $type === 'recorded' ? 'self-paced' : ($days . ' day(s)')
            ),
            'requirements' => [
                ['ar' => 'جهاز كمبيوتر أو لابتوب', 'en' => 'Laptop or computer'],
                ['ar' => 'اتصال إنترنت مستقر', 'en' => 'Stable internet connection'],
                ['ar' => 'الرغبة في التعلم والتطبيق', 'en' => 'Willingness to learn and practice'],
            ],
            'features' => [
                ['ar' => 'شهادة معتمدة بعد التقييم', 'en' => 'Certificate after rating'],
                ['ar' => 'محتوى تطبيقي حديث', 'en' => 'Modern practical content'],
                ['ar' => 'دعم المحاضر', 'en' => 'Trainer support'],
                ['ar' => 'اختبارات تقييمية', 'en' => 'Assessment exams'],
            ],
            'suitable_for' => [
                ['ar' => 'المبتدئون', 'en' => 'Beginners'],
                ['ar' => 'الباحثون عن تطوير المهارات', 'en' => 'Skill seekers'],
                ['ar' => 'أصحاب المشاريع', 'en' => 'Entrepreneurs'],
            ],
            'buttons' => [
                [
                    'text_ar' => 'دليل الدورة',
                    'text_en' => 'Course guide',
                    'link' => 'https://example.com/course-guide',
                    'color' => '#0D2444',
                    'needs_login' => false,
                ],
            ],
            'main_image' => $data['image'],
            'images' => null,
            'start_date' => $start,
            'end_date' => $end,
            'last_date' => $start->copy()->addHours(6),
            'status' => 'active',
            'trainer_id' => $trainer->id,
            'has_exam' => $type !== 'recorded',
            'required_exam_pass_count' => $type === 'recorded' ? null : max(1, (int) ceil($days * 0.7)),
            'rest_days' => [],
            'chat_locked_for_trainees' => false,
        ];

        $course = Course::firstOrCreate(['name_en' => $seedKey], $attrs);

        if (Str::startsWith((string) $course->name_en, 'SEED-')) {
            $course->forceFill(array_merge($attrs, [
                'main_image' => $course->main_image ?: $data['image'],
            ]))->save();
        }

        return $course->fresh();
    }

    /**
     * Create one day-exam (with questions/answers) for each course day.
     */
    protected function seedDayExamsForCourse(Course $course, int $days): void
    {
        $days = max(1, $days);

        for ($day = 1; $day <= $days; $day++) {
            $exam = CourseDayExam::firstOrCreate(
                [
                    'course_id' => $course->id,
                    'day_index' => $day,
                ],
                [
                    'sort_order' => $day - 1,
                    'title' => 'اختبار اليوم ' . $day,
                    'pass_score' => 2,
                    'duration_minutes' => 20,
                    'started_at' => null,
                    'ended_at' => null,
                    'skipped_at' => null,
                ]
            );

            if ($exam->questions()->exists()) {
                continue;
            }

            $bank = $this->questionBank($course->name_ar, $day);
            foreach ($bank as $qi => $item) {
                $question = CourseDayExamQuestion::create([
                    'course_day_exam_id' => $exam->id,
                    'question' => $item['q'],
                    'sort_order' => $qi,
                ]);

                foreach ($item['answers'] as $ai => $answer) {
                    CourseDayExamAnswer::create([
                        'question_id' => $question->id,
                        'answer' => $answer['text'],
                        'is_correct' => $answer['correct'],
                        'sort_order' => $ai,
                    ]);
                }
            }
        }

        $course->forceFill([
            'count_days' => $days,
            'has_exam' => true,
            'required_exam_pass_count' => max(1, (int) ceil($days * 0.7)),
        ])->save();
    }

    /**
     * Build units → lessons + unit exams for recorded courses.
     */
    protected function seedRecordedPath(Course $course): void
    {
        if ($course->units()->exists()) {
            // Still ensure each exam item has questions
            foreach ($course->units as $unit) {
                foreach ($unit->items()->where('type', 'exam')->get() as $examItem) {
                    $this->ensurePathExamQuestions($examItem);
                }
            }

            return;
        }

        $videos = [
            'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
            'https://www.youtube.com/watch?v=LXb3EKWsInQ',
            'https://www.youtube.com/watch?v=ScMzIvxBSi4',
            'https://www.youtube.com/watch?v=jNQXAC9IVRw',
        ];

        $unitsPlan = [
            [
                'title_ar' => 'الوحدة الأولى: الأساسيات',
                'title_en' => 'Unit 1: Fundamentals',
                'lessons' => [
                    ['title_ar' => 'مقدمة الدورة', 'title_en' => 'Course introduction', 'duration' => 420],
                    ['title_ar' => 'المفاهيم الأساسية', 'title_en' => 'Core concepts', 'duration' => 780],
                ],
            ],
            [
                'title_ar' => 'الوحدة الثانية: التطبيق العملي',
                'title_en' => 'Unit 2: Practical application',
                'lessons' => [
                    ['title_ar' => 'تطبيق عملي 1', 'title_en' => 'Practice 1', 'duration' => 900],
                    ['title_ar' => 'تطبيق عملي 2', 'title_en' => 'Practice 2', 'duration' => 840],
                ],
            ],
            [
                'title_ar' => 'الوحدة الثالثة: المشروع النهائي',
                'title_en' => 'Unit 3: Final project',
                'lessons' => [
                    ['title_ar' => 'بناء المشروع', 'title_en' => 'Building the project', 'duration' => 1200],
                    ['title_ar' => 'مراجعة وتسليم', 'title_en' => 'Review and delivery', 'duration' => 600],
                ],
            ],
        ];

        $totalSeconds = 0;
        $videoIndex = 0;

        foreach ($unitsPlan as $ui => $plan) {
            $unit = CourseUnit::create([
                'course_id' => $course->id,
                'title_ar' => $plan['title_ar'],
                'title_en' => $plan['title_en'],
                'sort_order' => $ui,
            ]);

            $itemOrder = 0;
            foreach ($plan['lessons'] as $lesson) {
                CoursePathItem::create([
                    'unit_id' => $unit->id,
                    'type' => 'lesson',
                    'title_ar' => $lesson['title_ar'],
                    'title_en' => $lesson['title_en'],
                    'video_embed_url' => $videos[$videoIndex % count($videos)],
                    'video_duration_seconds' => $lesson['duration'],
                    'sort_order' => $itemOrder++,
                ]);
                $totalSeconds += $lesson['duration'];
                $videoIndex++;
            }

            $examItem = CoursePathItem::create([
                'unit_id' => $unit->id,
                'type' => 'exam',
                'title_ar' => 'اختبار ' . $plan['title_ar'],
                'title_en' => 'Exam — ' . $plan['title_en'],
                'exam_pass_score' => 2,
                'exam_duration_minutes' => 15,
                'sort_order' => $itemOrder,
            ]);

            $this->ensurePathExamQuestions($examItem, $ui + 1);
        }

        $course->forceFill([
            'total_video_duration_seconds' => $totalSeconds,
            'has_exam' => false,
            'count_days' => 0,
        ])->save();
    }

    protected function ensurePathExamQuestions(CoursePathItem $examItem, int $unitIndex = 1): void
    {
        if ($examItem->examQuestions()->exists()) {
            return;
        }

        $bank = $this->questionBank('المسار التعليمي', $unitIndex);
        foreach ($bank as $qi => $item) {
            $question = CoursePathExamQuestion::create([
                'path_item_id' => $examItem->id,
                'question' => $item['q'],
                'sort_order' => $qi,
            ]);

            foreach ($item['answers'] as $ai => $answer) {
                CoursePathExamAnswer::create([
                    'question_id' => $question->id,
                    'answer' => $answer['text'],
                    'is_correct' => $answer['correct'],
                    'sort_order' => $ai,
                ]);
            }
        }
    }

    protected function questionBank(string $context, int $day): array
    {
        return [
            [
                'q' => "ما الهدف الأساسي من {$context} — اليوم {$day}؟",
                'answers' => [
                    ['text' => 'فهم المفاهيم الأساسية وتطبيقها', 'correct' => true],
                    ['text' => 'حفظ المصطلحات فقط دون تطبيق', 'correct' => false],
                    ['text' => 'تخطي المحتوى النظري بالكامل', 'correct' => false],
                    ['text' => 'الاعتماد على التخمين', 'correct' => false],
                ],
            ],
            [
                'q' => "أي مما يلي يُعد أفضل ممارسة أثناء التعلم في اليوم {$day}؟",
                'answers' => [
                    ['text' => 'التطبيق المباشر بعد كل مفهوم', 'correct' => true],
                    ['text' => 'تأجيل التطبيق حتى نهاية الدورة', 'correct' => false],
                    ['text' => 'تجاهل الأمثلة العملية', 'correct' => false],
                    ['text' => 'عدم تدوين الملاحظات', 'correct' => false],
                ],
            ],
            [
                'q' => "كيف تقيّم فهمك لمحتوى اليوم {$day}؟",
                'answers' => [
                    ['text' => 'بإنجاز تمرين قصير والتحقق من النتيجة', 'correct' => true],
                    ['text' => 'بعدم مراجعة أي شيء', 'correct' => false],
                    ['text' => 'بتخطي الاختبار', 'correct' => false],
                    ['text' => 'بالاعتماد على الحظ', 'correct' => false],
                ],
            ],
        ];
    }

    protected function seedTrainees(): array
    {
        $profiles = [
            ['name' => 'هند الزعابي', 'email' => 'seed.trainee.1@academy.test', 'country' => 'AE'],
            ['name' => 'ماجد العلي', 'email' => 'seed.trainee.2@academy.test', 'country' => 'SA'],
            ['name' => 'رانية حسين', 'email' => 'seed.trainee.3@academy.test', 'country' => 'EG'],
            ['name' => 'فهد المطيري', 'email' => 'seed.trainee.4@academy.test', 'country' => 'KW'],
            ['name' => 'سلمى جابر', 'email' => 'seed.trainee.5@academy.test', 'country' => 'JO'],
            ['name' => 'باسل خوري', 'email' => 'seed.trainee.6@academy.test', 'country' => 'LB'],
            ['name' => 'دانة الشمري', 'email' => 'seed.trainee.7@academy.test', 'country' => 'QA'],
            ['name' => 'إياد منصور', 'email' => 'seed.trainee.8@academy.test', 'country' => 'BH'],
        ];

        $trainees = [];
        foreach ($profiles as $i => $profile) {
            $trainees[] = User::firstOrCreate(
                ['email' => $profile['email']],
                [
                    'name' => $profile['name'],
                    'account_type' => 'personal',
                    'password' => Hash::make('password'),
                    'phone' => '+97150' . str_pad((string) (2000000 + $i), 7, '0', STR_PAD_LEFT),
                    'country' => $profile['country'],
                    'role' => 'trainee',
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'whatsapp_verified' => true,
                ]
            );
        }

        return $trainees;
    }

    protected function seedReviews(array $courses, array $trainees): void
    {
        if (empty($courses) || empty($trainees)) {
            return;
        }

        $feedbackPool = [
            'دورة ممتازة وتنظيم رائع، استفدت كثيراً من المحتوى العملي.',
            'المحاضر واضح والأسلوب سلس، أنصح الجميع بالالتحاق.',
            'تجربة تعليمية غنية والمنصة سهلة الاستخدام.',
            'المحتوى حديث ويغطي احتياجات السوق الحالية.',
            'أحسن دورة حضرتها هذا العام، التطبيقات العملية قوية.',
            'شرح مرتب وأمثلة واقعية ساعدتني في العمل مباشرة.',
            'التنظيم ممتاز والدعم سريع عند أي استفسار.',
            'أنصح بها بقوة لكل من يريد تطوير مهاراته.',
        ];

        // Unique trainee×course pairs so re-runs don't collide on unique(course_id,user_id)
        $targetCourses = collect($courses)->values();
        $pairs = [];
        foreach ($targetCourses as $ci => $course) {
            foreach ($trainees as $ti => $trainee) {
                $pairs[] = [$course, $trainee, count($pairs)];
                if (count($pairs) >= 24) {
                    break 2;
                }
            }
        }

        foreach ($pairs as [$course, $trainee, $i]) {
            $payment = Payment::firstOrCreate(
                [
                    'user_id' => $trainee->id,
                    'course_id' => $course->id,
                    'payment_id' => 'SEED-PAY-' . $course->id . '-' . $trainee->id,
                ],
                [
                    'amount' => (float) $course->price,
                    'original_price' => (float) $course->price,
                    'fees' => 0,
                    'status' => 'success',
                    'payment_method' => 'seed',
                    'currency' => 'AED',
                    'is_attended' => true,
                    'path_completed_at' => $course->isRecorded() ? now()->subDays(2) : null,
                ]
            );

            $answers = $this->buildAnswersForCourse($course, $feedbackPool[$i % count($feedbackPool)], $i);

            CourseRating::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'user_id' => $trainee->id,
                ],
                [
                    'payment_id' => $payment->id,
                    'answers' => $answers,
                    'completed_at' => now()->subDays(($i % 10) + 1),
                    'is_featured' => $i < 12,
                ]
            );
        }
    }

    protected function buildAnswersForCourse(Course $course, string $feedback, int $seed): array
    {
        $type = $course->location_type ?: 'online';
        $questions = config("course_rating.{$type}", config('course_rating.online', []));
        $answers = [];

        // Deterministic but varied scores 3–5
        $base = 3 + ($seed % 3);

        foreach ($questions as $q) {
            $id = $q['id'] ?? null;
            if (!$id) {
                continue;
            }
            $qType = $q['type'] ?? 'text';
            if ($qType === 'scale') {
                $answers[$id] = min(5, max(1, $base + (($seed + strlen($id)) % 2)));
            } elseif ($qType === 'boolean') {
                $answers[$id] = ($seed % 7 === 0) ? '0' : '1';
            } else {
                $answers[$id] = $id === 'suggestions'
                    ? 'مقترح: إضافة المزيد من التطبيقات العملية.'
                    : $feedback;
            }
        }

        return $answers;
    }
}
