<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'location_type',
        'online_link',
        'venue_name',
        'venue_map_url',
        'venue_details',
        'counter',
        'count_days',
        'external_url',
        'service_id',
        'price',
        'description_ar',
        'description_en',
        'requirements',
        'features',
        'buttons',
        'main_image',
        'images',
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
    ];

    protected $casts = [
        'system_external' => 'boolean',
        'price' => 'decimal:2',
        'requirements' => 'array',
        'features' => 'array',
        'buttons' => 'array',
        'images' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'last_date' => 'datetime',
        'counter' => 'integer',
        'count_days' => 'integer',
        'rest_days' => 'array',
        'has_exam' => 'boolean',
        'exam_pass_score' => 'integer',
        'exam_duration_minutes' => 'integer',
        'exam_started_at' => 'datetime',
        'exam_ended_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
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

    public function isExamStarted(): bool
    {
        return $this->has_exam && $this->exam_started_at !== null && $this->exam_ended_at === null;
    }

    /**
     * Exam lifecycle: none | not_started | running | finished
     */
    public function examStatus(): string
    {
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

    public function userPassedExam(?int $userId = null): bool
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
     * Per-user exam progress: none | not_entered | in_progress | passed | failed
     */
    public function userExamStatus(?int $userId = null, $attempt = null): string
    {
        $userId = $userId ?? auth()->id();
        if (!$userId || !$this->has_exam) {
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
