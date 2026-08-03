<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'system_id',
        'payment_id',
        'course_id',
        'private_course_request_id',
        'last_path_item_id',
        'path_completed_at',
        'special_request_id',
        'request_payment_id',
        'amount',
        'original_price',
        'trainer_profit_pct',
        'trainer_profit_amount',
        'platform_profit_amount',
        'fees',
        'status',
        'store_id',
        'payment_method',
        'currency',
        'is_attended',
    ];

    protected $casts = [
        'is_attended' => 'boolean',
        'path_completed_at' => 'datetime',
        'trainer_profit_pct' => 'decimal:2',
        'trainer_profit_amount' => 'decimal:2',
        'platform_profit_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // داخل ملف Payment.php
    public function requestPayment()
    {
        return $this->belongsTo(RequestPayment::class, 'request_payment_id');
    }
    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function privateCourseRequest()
    {
        return $this->belongsTo(PrivateCourseRequest::class, 'private_course_request_id');
    }

    public function specialCertificate()
    {
        return $this->hasOne(CourseSpecialCertificate::class);
    }

    public function store()
    {
        return $this->belongsTo(MyStore::class);
    }
    // App\Models\Payment.php

    public function specialRequest()
    {
        return $this->belongsTo(SpecialRequest::class, 'special_request_id');
    }

    /**
     * Internal invoice number shown to users (never the payment-gateway intent id).
     */
    public function invoiceNumber(): string
    {
        return 'INV-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Whether this enrollment's course is ended for the learner.
     * Recorded courses end at 100% path completion; live/on-site use end_date.
     */
    public function isCourseEndedForLearner(?\Carbon\Carbon $now = null): bool
    {
        $course = $this->course;
        if (!$course) {
            return false;
        }

        $now = $now ?? now();

        if ($course->isRecorded()) {
            if ($this->path_completed_at) {
                return true;
            }

            try {
                return $course->isPathFullyCompletedBy((int) $this->user_id);
            } catch (\Throwable) {
                return false;
            }
        }

        if (empty($course->end_date)) {
            return false;
        }

        return $now->gt(\Carbon\Carbon::parse($course->end_date));
    }

    /**
     * Whether this enrollment is currently active for the learner.
     */
    public function isCourseActiveForLearner(?\Carbon\Carbon $now = null): bool
    {
        $course = $this->course;
        if (!$course) {
            return false;
        }

        $now = $now ?? now();

        if ($course->isRecorded()) {
            return !$this->isCourseEndedForLearner($now);
        }

        if (empty($course->start_date) || empty($course->end_date)) {
            return false;
        }

        return $now->between(
            \Carbon\Carbon::parse($course->start_date),
            \Carbon\Carbon::parse($course->end_date)
        );
    }

    /**
     * Upcoming applies to scheduled live/on-site courses only.
     */
    public function isCourseUpcomingForLearner(?\Carbon\Carbon $now = null): bool
    {
        $course = $this->course;
        if (!$course || $course->isRecorded() || empty($course->start_date)) {
            return false;
        }

        $now = $now ?? now();

        return $now->lt(\Carbon\Carbon::parse($course->start_date));
    }
}
