<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'location_type',
        'levels',
        'online_link',
        'venue_name',
        'venue_map_url',
        'venue_details',
        'counter',
        'count_days',
        'external_url',
        'service_id',
        'course_category_id',
        'price',
        'description_ar',
        'description_en',
        'requirements',
        'features',
        'suitable_for',
        'buttons',
        'main_image',
        'images',
        'video',
        'total_video_duration_seconds',
        'start_date',
        'end_date',
        'last_date',
        'status',
        'rest_days',
        'has_exam',
        'exam_pass_score',
        'exam_duration_minutes',
        'exam_started_at',
        'exam_ended_at',
        'required_exam_pass_count',
        'trainer_id',
        'chat_locked_for_trainees',
        'allows_private_requests',
        'private_course_price',
        'private_of_course_id',
        'canceled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'system_external' => 'boolean',
        'chat_locked_for_trainees' => 'boolean',
        'allows_private_requests' => 'boolean',
        'price' => 'decimal:2',
        'private_course_price' => 'decimal:2',
        'requirements' => 'array',
        'features' => 'array',
        'suitable_for' => 'array',
        'buttons' => 'array',
        'images' => 'array',
        'levels' => 'array',
        'total_video_duration_seconds' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'last_date' => 'datetime',
        'canceled_at' => 'datetime',
        'counter' => 'integer',
        'count_days' => 'integer',
        'rest_days' => 'array',
        'has_exam' => 'boolean',
        'exam_pass_score' => 'integer',
        'exam_duration_minutes' => 'integer',
        'exam_started_at' => 'datetime',
        'exam_ended_at' => 'datetime',
        'required_exam_pass_count' => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function units()
    {
        return $this->hasMany(CourseUnit::class)->orderBy('sort_order');
    }

    public function isRecorded(): bool
    {
        return $this->location_type === 'recorded';
    }

    public function isOnline(): bool
    {
        return $this->location_type === 'online';
    }

    public function isOnSite(): bool
    {
        return $this->location_type === 'on_site';
    }

    public function isPrivate(): bool
    {
        return $this->location_type === 'private';
    }

    public function isCanceled(): bool
    {
        return $this->canceled_at !== null || $this->status === 'canceled';
    }

    /**
     * Whether this course should appear in public academy listings.
     * Expired online/onsite without private requests are hidden.
     */
    public function isPubliclyListable(): bool
    {
        if ($this->isPrivate() || $this->isCanceled()) {
            return false;
        }

        if ($this->status !== 'active') {
            return false;
        }

        if ($this->isRegistrationClosed() && ! $this->allows_private_requests) {
            return false;
        }

        return true;
    }

    public function scopePubliclyListable($query)
    {
        return $query
            ->where('status', 'active')
            ->whereNull('canceled_at')
            ->where('location_type', '!=', 'private')
            ->where(function ($q) {
                $q->where('location_type', 'recorded')
                    ->orWhereNull('last_date')
                    ->orWhere('last_date', '>=', now())
                    ->orWhere('allows_private_requests', true);
            });
    }

    public function privateOfCourse()
    {
        return $this->belongsTo(self::class, 'private_of_course_id');
    }

    public function privateClones()
    {
        return $this->hasMany(self::class, 'private_of_course_id');
    }

    public function privateCourseRequests()
    {
        return $this->hasMany(PrivateCourseRequest::class, 'source_course_id');
    }

    /** Online / on-site courses use last_date as the enrollment deadline. */
    public function hasRegistrationDeadline(): bool
    {
        return ! $this->isRecorded() && ! $this->isPrivate() && $this->last_date !== null;
    }

    public function isRegistrationClosed(): bool
    {
        return $this->hasRegistrationDeadline()
            && now()->greaterThan($this->last_date);
    }

    /**
     * Flattened path items in curriculum order (unit order → item order).
     */
    public function orderedPathItems()
    {
        try {
            $this->loadMissing('units.items');

            return $this->units->flatMap(fn (CourseUnit $unit) => $unit->items);
        } catch (\Throwable) {
            return collect();
        }
    }

    public function formattedTotalVideoDuration(): string
    {
        return $this->formatDurationClock((int) ($this->total_video_duration_seconds ?? 0));
    }

    /**
     * Videos + exams total duration for recorded-course path UI.
     */
    public function formattedTotalContentDuration(): string
    {
        return $this->formatDurationClock($this->totalContentDurationSeconds());
    }

    protected function formatDurationClock(int $seconds): string
    {
        if ($seconds <= 0) {
            return '—';
        }

        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;

        if ($h > 0) {
            return sprintf('%d:%02d:%02d', $h, $m, $s);
        }

        return sprintf('%d:%02d', $m, $s);
    }

    /**
     * Whether trainees must finish each path step before unlocking the next.
     */
    public function requiresPathLessonComplete(): bool
    {
        return (bool) config('courses.path_require_lesson_complete', true);
    }

    /**
     * Fraction of video duration required to mark a lesson complete (e.g. 0.9).
     * Applied to both actual play time and farthest timeline position.
     */
    public function pathLessonCompleteRatio(): float
    {
        $ratio = (float) config('courses.path_lesson_complete_ratio', 0.9);

        return max(0.1, min(1.0, $ratio));
    }

    /**
     * Whether the user may open this path item (previous items completed).
     */
    public function canUserAccessPathItem(User $user, CoursePathItem $item): bool
    {
        $ordered = $this->orderedPathItems();
        $index = $ordered->search(fn ($i) => (int) $i->id === (int) $item->id);
        if ($index === false) {
            return false;
        }

        if (!$this->requiresPathLessonComplete()) {
            return true;
        }

        if ($index === 0) {
            return true;
        }

        $previous = $ordered->slice(0, $index);
        $completedIds = CoursePathProgress::where('user_id', $user->id)
            ->where('course_id', $this->id)
            ->where('is_completed', true)
            ->pluck('path_item_id')
            ->all();

        foreach ($previous as $prev) {
            if (!in_array((int) $prev->id, array_map('intval', $completedIds), true)) {
                return false;
            }
        }

        return true;
    }

    public function pathProgressForUser(?int $userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return collect();
        }

        try {
            return CoursePathProgress::where('course_id', $this->id)
                ->where('user_id', $userId)
                ->get()
                ->keyBy('path_item_id');
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * Completion stats for the recorded-course learning path.
     *
     * @return array{total:int,completed:int,percent:int}
     */
    public function pathCompletionForUser(?int $userId = null): array
    {
        $items = $this->orderedPathItems();
        $total = $items->count();
        if ($total === 0) {
            return ['total' => 0, 'completed' => 0, 'percent' => 0];
        }

        $progress = $this->pathProgressForUser($userId);
        $completed = $items->filter(function ($item) use ($progress) {
            return (bool) ($progress->get($item->id)?->is_completed);
        })->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'percent' => (int) round(($completed / $total) * 100),
        ];
    }

    /**
     * True when every path step is completed for this user.
     */
    public function isPathFullyCompletedBy(?int $userId = null): bool
    {
        if (!$this->isRecorded()) {
            return false;
        }

        $stats = $this->pathCompletionForUser($userId);

        return $stats['total'] > 0 && $stats['completed'] >= $stats['total'];
    }

    public function chatMessages()
    {
        return $this->hasMany(CourseChatMessage::class)->latest();
    }

    public function chatBlocks()
    {
        return $this->hasMany(CourseChatBlock::class);
    }

    public function isUserChatBlocked(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return false;
        }

        return $this->chatBlocks()->where('user_id', $userId)->exists();
    }

    /**
     * Can join live lecture room / post-course chat archive.
     * Admin, assigned trainer, or attended enrolled learner.
     */
    public function canAccessLectureChat(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return false;
        }

        if ($user->isAdmin() || $user->managesCourse($this)) {
            return true;
        }

        if (!$user->canLearnCourses()) {
            return false;
        }

        return Payment::where('course_id', $this->id)
            ->where('user_id', $user->id)
            ->where('is_attended', true)
            ->whereIn('status', ['completed', 'success', 'paid', 'active', 'pending'])
            ->exists();
    }

    public function canModerateChat(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return false;
        }

        return $user->isAdmin() || $user->managesCourse($this);
    }

    /**
     * Whether a user may post a chat message (moderators always can).
     */
    public function canSendChatMessage(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user || !$this->canAccessLectureChat($user)) {
            return false;
        }

        if ($this->canModerateChat($user)) {
            return true;
        }

        if ($this->chat_locked_for_trainees) {
            return false;
        }

        return !$this->isUserChatBlocked($user->id);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'course_id');
    }

    public function examQuestions()
    {
        return $this->hasMany(CourseExamQuestion::class)->orderBy('sort_order');
    }

    public function examAttempts()
    {
        return $this->hasMany(CourseExamAttempt::class);
    }

    public function dayExams()
    {
        return $this->hasMany(CourseDayExam::class)
            ->orderBy('day_index')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function dayExamAttempts()
    {
        return $this->hasMany(CourseDayExamAttempt::class);
    }

    public function ratings()
    {
        return $this->hasMany(CourseRating::class);
    }

    public function wishlists()
    {
        return $this->hasMany(CourseWishlist::class);
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'course_wishlists')
            ->withTimestamps();
    }

    public function featuredRatings()
    {
        return $this->hasMany(CourseRating::class)
            ->where('is_featured', true)
            ->whereNotNull('completed_at')
            ->latest('completed_at');
    }

    public function completedRatings()
    {
        return $this->hasMany(CourseRating::class)
            ->whereNotNull('completed_at')
            ->latest('completed_at');
    }

    /**
     * Average of all scale answers across completed trainee ratings.
     */
    public function averageRatingScore(): ?float
    {
        $ratings = $this->relationLoaded('completedRatings')
            ? $this->completedRatings
            : $this->completedRatings()->get();

        $scores = $ratings
            ->map(fn (CourseRating $r) => $r->averageScaleScore())
            ->filter(fn ($s) => $s !== null);

        if ($scores->isEmpty()) {
            return null;
        }

        return round($scores->avg(), 1);
    }

    public function completedRatingsCount(): int
    {
        if ($this->relationLoaded('completedRatings')) {
            return $this->completedRatings->count();
        }

        if (isset($this->completed_ratings_count)) {
            return (int) $this->completed_ratings_count;
        }

        return $this->completedRatings()->count();
    }

    /**
     * @return array<int, array{key: string, label_ar: string, label_en: string}>
     */
    public static function levelOptions(): array
    {
        return [
            ['key' => 'beginner', 'label_ar' => 'مبتدئ', 'label_en' => 'Beginner'],
            ['key' => 'intermediate', 'label_ar' => 'متوسط', 'label_en' => 'Intermediate'],
            ['key' => 'advanced', 'label_ar' => 'متقدم', 'label_en' => 'Advanced'],
            ['key' => 'all', 'label_ar' => 'كل المستويات', 'label_en' => 'All levels'],
        ];
    }

    /**
     * @return list<string>
     */
    public function levelKeys(): array
    {
        $levels = $this->levels ?? [];
        if (! is_array($levels)) {
            return [];
        }

        $allowed = collect(self::levelOptions())->pluck('key')->all();

        return array_values(array_intersect($levels, $allowed));
    }

    public function levelLabels(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $map = collect(self::levelOptions())->keyBy('key');

        return collect($this->levelKeys())
            ->map(function ($key) use ($map, $locale) {
                $opt = $map->get($key);
                if (! $opt) {
                    return null;
                }

                return $locale === 'en' ? $opt['label_en'] : $opt['label_ar'];
            })
            ->filter()
            ->values()
            ->all();
    }

    public function isFree(): bool
    {
        return (float) $this->price <= 0;
    }

    public function pathLessonCount(): int
    {
        $this->loadMissing('units.items');

        return $this->units->sum(fn ($unit) => $unit->items->where('type', 'lesson')->count());
    }

    public function pathExamCount(): int
    {
        $this->loadMissing('units.items');

        return $this->units->sum(fn ($unit) => $unit->items->where('type', 'exam')->count());
    }

    /**
     * Sum of exam time limits (minutes → seconds) across the educational path.
     */
    public function pathExamDurationSeconds(): int
    {
        $this->loadMissing('units.items');

        return (int) $this->units->sum(function ($unit) {
            return $unit->items
                ->where('type', 'exam')
                ->sum(fn ($item) => max(0, (int) ($item->exam_duration_minutes ?? 0)) * 60);
        });
    }

    /**
     * Video lesson seconds + exam duration seconds.
     */
    public function totalContentDurationSeconds(): int
    {
        $this->loadMissing('units.items');

        $videoSeconds = (int) $this->units->sum(function ($unit) {
            return $unit->items
                ->where('type', 'lesson')
                ->sum(fn ($item) => max(0, (int) ($item->video_duration_seconds ?? 0)));
        });

        // Prefer live sum; fall back to cached column if items lack durations
        if ($videoSeconds <= 0) {
            $videoSeconds = (int) ($this->total_video_duration_seconds ?? 0);
        }

        return $videoSeconds + $this->pathExamDurationSeconds();
    }

    /**
     * Average of featured public reviews (uses overall score, fallback average of scales).
     */
    public function featuredAverageRating(): ?float
    {
        $ratings = $this->relationLoaded('featuredRatings')
            ? $this->featuredRatings
            : $this->featuredRatings()->with('user')->get();

        $scores = $ratings->map(fn (CourseRating $r) => $r->overallScore())->filter(fn ($s) => $s !== null);
        if ($scores->isEmpty()) {
            return null;
        }

        return round($scores->avg(), 1);
    }

    public function featuredRatingsCount(): int
    {
        if ($this->relationLoaded('featuredRatings')) {
            return $this->featuredRatings->count();
        }

        return $this->featuredRatings()->count();
    }

    public function formattedTotalVideoDurationArabic(): string
    {
        return $this->formatDurationSecondsArabic((int) ($this->total_video_duration_seconds ?? 0));
    }

    public function formattedTotalContentDurationArabic(): string
    {
        return $this->formatDurationSecondsArabic($this->totalContentDurationSeconds());
    }

    protected function formatDurationSecondsArabic(int $seconds): string
    {
        if ($seconds <= 0) {
            return '—';
        }

        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;
        $parts = [];
        if ($h > 0) {
            $parts[] = $h . ' ساعة';
        }
        if ($m > 0) {
            $parts[] = $m . ' دقيقة';
        }
        if ($s > 0 && $h === 0) {
            $parts[] = $s . ' ثانية';
        }

        return $parts ? implode(' و ', $parts) : '—';
    }

    /**
     * Teaching calendar days (Carbon instances), excluding rest weekdays.
     *
     * @return \Illuminate\Support\Collection<int, \Carbon\Carbon>
     */
    public static function listTeachingDays($start, $end, array $restDays = [])
    {
        if (!$start || !$end) {
            return collect();
        }

        $start = \Carbon\Carbon::parse($start)->startOfDay();
        $end = \Carbon\Carbon::parse($end)->startOfDay();
        if ($end->lt($start)) {
            return collect();
        }

        $restDays = array_map('strtolower', $restDays);
        $days = collect();
        $current = $start->copy();

        while ($current->lte($end)) {
            $dayName = strtolower($current->format('l'));
            if (!in_array($dayName, $restDays, true)) {
                $days->push($current->copy());
            }
            $current->addDay();
        }

        return $days;
    }

    public function teachingDays()
    {
        return self::listTeachingDays($this->start_date, $this->end_date, $this->rest_days ?? []);
    }

    public function usesDayExams(): bool
    {
        if ($this->isRecorded()) {
            return false;
        }

        try {
            return $this->dayExams()->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    public function hasLiveExams(): bool
    {
        return !$this->isRecorded() && ($this->usesDayExams() || (bool) $this->has_exam);
    }

    public function orderedDayExams()
    {
        $this->loadMissing('dayExams.questions');

        return $this->dayExams;
    }

    public function runningDayExam(): ?CourseDayExam
    {
        try {
            return $this->dayExams()
                ->whereNotNull('started_at')
                ->whereNull('ended_at')
                ->whereNull('skipped_at')
                ->orderBy('day_index')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();
        } catch (\Throwable) {
            return null;
        }
    }

    public function lastDayExam(): ?CourseDayExam
    {
        return $this->dayExams()
            ->orderByDesc('day_index')
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->first();
    }

    public function areAllDayExamsFinished(): bool
    {
        $exams = $this->dayExams;
        if ($exams->isEmpty()) {
            return false;
        }

        return $exams->every(fn (CourseDayExam $exam) => $exam->isFinished());
    }

    public function nonSkippedDayExamsCount(): int
    {
        return $this->dayExams->filter(fn (CourseDayExam $e) => !$e->isSkipped())->count();
    }

    public function effectiveRequiredExamPassCount(): int
    {
        $nonSkipped = $this->nonSkippedDayExamsCount();
        if ($nonSkipped <= 0) {
            return 0;
        }

        $required = (int) ($this->required_exam_pass_count ?? 1);

        return max(1, min($required, $nonSkipped));
    }

    public function userPassedDayExamCount(?int $userId = null): int
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return 0;
        }

        $skippedIds = $this->dayExams->whereNotNull('skipped_at')->pluck('id');

        return $this->dayExamAttempts()
            ->where('user_id', $userId)
            ->where('passed', true)
            ->whereNotNull('submitted_at')
            ->when($skippedIds->isNotEmpty(), fn ($q) => $q->whereNotIn('course_day_exam_id', $skippedIds))
            ->count();
    }

    public function userMetExamPassRequirement(?int $userId = null): bool
    {
        if ($this->isRecorded()) {
            return true;
        }

        if (!$this->usesDayExams()) {
            return !$this->has_exam || $this->userPassedLegacyExam($userId);
        }

        $required = $this->effectiveRequiredExamPassCount();
        if ($required <= 0) {
            return true;
        }

        return $this->userPassedDayExamCount($userId) >= $required;
    }

    public function userCompletedRating(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return true;
        }

        try {
            return $this->ratings()
                ->where('user_id', $userId)
                ->whereNotNull('completed_at')
                ->exists();
        } catch (\Throwable) {
            return true;
        }
    }

    public function userCanGetCertificate(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return false;
        }

        try {
            if ($this->isRecorded()) {
                return $this->isPathFullyCompletedBy($userId)
                    && $this->userCompletedRating($userId);
            }

            if (!$this->usesDayExams()) {
                $examOk = !$this->has_exam || $this->userPassedLegacyExam($userId);
                return $examOk && $this->userCompletedRating($userId);
            }

            return $this->areAllDayExamsFinished()
                && $this->userMetExamPassRequirement($userId)
                && $this->userCompletedRating($userId);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Whether the trainee is eligible to rate but hasn't yet.
     */
    public function userNeedsRating(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return false;
        }

        try {
            if ($this->userCompletedRating($userId)) {
                return false;
            }

            if ($this->isRecorded()) {
                return $this->isPathFullyCompletedBy($userId);
            }

            if ($this->usesDayExams()) {
                return $this->areAllDayExamsFinished();
            }

            // Online / on_site without day exams: after end_date
            return $this->end_date && now()->greaterThan($this->end_date);
        } catch (\Throwable) {
            return false;
        }
    }

    public function previousDayExam(CourseDayExam $exam): ?CourseDayExam
    {
        $ordered = $this->orderedDayExams()->values();
        $index = $ordered->search(fn (CourseDayExam $e) => (int) $e->id === (int) $exam->id);
        if ($index === false || $index === 0) {
            return null;
        }

        return $ordered->get($index - 1);
    }

    public function canStartDayExam(CourseDayExam $exam): bool
    {
        if ($this->isRecorded() || !$exam->isPending()) {
            return false;
        }

        if ($exam->questions()->count() < 1) {
            return false;
        }

        if ($this->runningDayExam()) {
            return false;
        }

        $previous = $this->previousDayExam($exam);

        return !$previous || $previous->isFinished();
    }

    public function canSkipDayExam(CourseDayExam $exam): bool
    {
        if ($this->isRecorded() || !$exam->isPending()) {
            return false;
        }

        $previous = $this->previousDayExam($exam);

        return !$previous || $previous->isFinished();
    }

    public function isExamStarted(): bool
    {
        if ($this->usesDayExams()) {
            return $this->runningDayExam() !== null;
        }

        return $this->has_exam && $this->exam_started_at !== null && $this->exam_ended_at === null;
    }

    /**
     * Course-level aggregate: none | not_started | running | finished
     */
    public function examStatus(): string
    {
        if ($this->usesDayExams()) {
            $exams = $this->dayExams;
            if ($exams->isEmpty()) {
                return 'none';
            }
            if ($exams->contains(fn (CourseDayExam $e) => $e->isRunning())) {
                return 'running';
            }
            if ($exams->every(fn (CourseDayExam $e) => $e->isFinished())) {
                return 'finished';
            }

            return 'not_started';
        }

        if (!$this->has_exam) {
            return 'none';
        }

        if ($this->exam_ended_at) {
            return 'finished';
        }

        if ($this->exam_started_at) {
            return 'running';
        }

        return 'not_started';
    }

    public function examStatusLabel(): string
    {
        return match ($this->examStatus()) {
            'not_started' => 'لم يبدأ',
            'running' => 'جارٍ',
            'finished' => 'منتهٍ',
            default => 'بدون اختبار',
        };
    }

    public function courseStatusLabel(): string
    {
        return $this->status === 'active' ? 'نشط' : 'غير نشط';
    }

    /**
     * True once the course start datetime has been reached.
     * Recorded courses have no live schedule, so settings stay editable.
     */
    public function hasBegun(): bool
    {
        if ($this->isRecorded()) {
            return false;
        }

        if (!$this->start_date) {
            return false;
        }

        return now()->greaterThanOrEqualTo($this->start_date);
    }

    protected function userPassedLegacyExam(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        if (!$userId || !$this->has_exam) {
            return false;
        }

        return $this->examAttempts()
            ->where('user_id', $userId)
            ->where('passed', true)
            ->whereNotNull('submitted_at')
            ->exists();
    }

    /**
     * Certificate gate helper (day exams + rating, or legacy single exam).
     */
    public function userPassedExam(?int $userId = null): bool
    {
        if ($this->usesDayExams()) {
            return $this->userCanGetCertificate($userId);
        }

        return $this->userPassedLegacyExam($userId);
    }

    /**
     * Per-user progress for a specific day exam, or aggregate for certificate eligibility.
     */
    public function userExamStatus(?int $userId = null, $attempt = null): string
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return 'none';
        }

        if ($this->usesDayExams()) {
            if (!$this->areAllDayExamsFinished()) {
                $running = $this->runningDayExam();
                if ($running) {
                    $attempt = $attempt ?? CourseDayExamAttempt::where('course_day_exam_id', $running->id)
                        ->where('user_id', $userId)
                        ->first();
                    if (!$attempt) {
                        return 'not_entered';
                    }
                    if (!$attempt->isSubmitted()) {
                        return 'in_progress';
                    }

                    return $attempt->passed ? 'passed' : 'failed';
                }

                return 'not_entered';
            }

            if (!$this->userCompletedRating($userId)) {
                return 'in_progress';
            }

            return $this->userMetExamPassRequirement($userId) ? 'passed' : 'failed';
        }

        if (!$this->has_exam) {
            return 'none';
        }

        $attempt = $attempt ?? $this->examAttempts->firstWhere('user_id', $userId);

        if (!$attempt) {
            return 'not_entered';
        }

        if (!$attempt->isSubmitted()) {
            return 'in_progress';
        }

        return $attempt->passed ? 'passed' : 'failed';
    }

    public function userExamStatusLabel(?int $userId = null, $attempt = null): string
    {
        return match ($this->userExamStatus($userId, $attempt)) {
            'not_entered' => 'لم يدخل بعد',
            'in_progress' => 'قيد الاختبار',
            'passed' => 'ناجح',
            'failed' => 'راسب',
            default => '—',
        };
    }

    public function isUserEnrolled()
    {
        if (!auth()->check()) return false;

        return $this->payments()
            ->where('user_id', auth()->id())
            ->whereIn('status', ['completed', 'success', 'paid', 'active', 'pending'])
            ->exists();
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user')->withPivot([
                'price_paid',
                'status',
                'enrolled_at',
                'expires_at'
            ])->withTimestamps();
    }

    public function getActualCourseDaysAttribute()
    {
        return $this->computeCourseDays($this->start_date, $this->end_date, $this->rest_days ?? []);
    }

    /**
     * Absolute public base URL, always the live domain (never localhost),
     * so links in emails/WhatsApp work even when generated from CLI/queue.
     */
    public static function publicBaseUrl(): string
    {
        $base = rtrim((string) config('app.url'), '/');
        if ($base === '' || str_contains($base, 'localhost') || str_contains($base, '127.0.0.1')) {
            $base = 'https://evorq.online';
        }
        return $base;
    }

    /**
     * Public (client-facing) URL for this course.
     */
    public function publicUrl(): string
    {
        return self::publicBaseUrl() . '/courses/' . $this->id;
    }

    /**
     * Absolute URL to the course main image (falls back to the logo).
     */
    public function mainImageUrl(): string
    {
        if (empty($this->main_image)) {
            return self::publicBaseUrl() . '/assets/images/logo.webp';
        }
        return self::publicBaseUrl() . '/storage/' . ltrim($this->main_image, '/');
    }

    /**
     * Absolute URL to the optional course promo video, or null if none.
     * Uses a short-lived signed stream route — never a public /storage/ file URL.
     */
    public function videoUrl(): ?string
    {
        if (empty($this->video)) {
            return null;
        }

        return self::signedPromoStreamUrl($this);
    }

    public static function signedPromoStreamUrl(self|int $course, int $minutes = 90): string
    {
        return URL::temporarySignedRoute(
            'courses.video.stream',
            now()->addMinutes($minutes),
            ['course' => $course instanceof self ? $course->id : $course],
        );
    }

    /**
     * Inclusive calendar days between start and end, minus matching rest weekdays.
     * Same calendar day => 1 day (not 2).
     */
    public static function computeCourseDays($start, $end, array $restDays = []): int
    {
        if (!$start || !$end) {
            return 0;
        }

        $start = \Carbon\Carbon::parse($start)->startOfDay();
        $end = \Carbon\Carbon::parse($end)->startOfDay();

        if ($end->lt($start)) {
            return 0;
        }

        $restDays = array_map('strtolower', $restDays);
        $total = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            $dayName = strtolower($current->format('l')); // sunday, monday, ...
            if (!in_array($dayName, $restDays, true)) {
                $total++;
            }
            $current->addDay();
        }

        return $total;
    }
}
