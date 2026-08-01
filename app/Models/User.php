<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\WithdrawalRequest;
use App\Models\Payment;
use App\Models\Requests;
use App\Models\System;
use App\Models\WorkTime;
use App\Models\Salary;
use App\Models\EmployeeAdjustment;
use App\Support\SystemManager;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'skills',
        'id_card_path',
        'id_card_front_path',
        'id_card_back_path',
        'terms_accepted_at',
        'verification_video',
        'name',
        'account_type',
        'company_name',
        'company_logo',
        'avatar',
        'course_category_id',
        'otp',
        'email',
        'password',
        'whatsapp_verified',
        'role',
        'status',
        'phone',
        'country',
        'percentage',
        'is_employee',
        'can_view_projects',
        'can_view_notes',
        'can_propose_quotes',
        'can_enter_knowledge_bank',
        'apply_working_hours',
        'can_request_meetings',
        'services_screen_available',
        'first_country',
        
        // --- الحقول الإدارية والملاحظات ---
        'note_title',
        'note_date',
        'note_details',
        'note_attachment',
        'is_visible_to_employee',

        // --- البيانات المالية والرواتب الأساسية ---
        'apply_salary_scale',
        'salary_year',
        'salary_month',
        'salary_attachment',
        'salary_notes',
        'salary_amount',
        'salary_currency',
        'hiring_date',

        // --- نظام ساعات العمل والدوام (الحقول الجديدة) ---
        'work_start_time',
        'work_end_time',
        'daily_work_hours',
        'break_minutes',
        'overtime_hourly_rate',

        // --- الخصومات والمدد المسموحة (الحقول الجديدة) ---
        'allowed_late_minutes',
        'morning_late_deduction',
        'break_late_deduction',
        'early_leave_deduction',

        // --- الإجازات (الحقول الجديدة) ---
        'vacation_days',

        // --- نظام حساب الراتب - Scale (الحقول الجديدة) ---
        'salary_amount_scale',
        'salary_currency_scale',
        'hiring_date_scale',
    ];

    protected $casts = [
        'vacation_days' => 'array',
        'is_employee' => 'boolean',
        'can_view_projects' => 'boolean',
        'can_view_notes' => 'boolean',
        'can_propose_quotes' => 'boolean',
        'can_enter_knowledge_bank' => 'boolean',
        'apply_working_hours' => 'boolean',
        'hiring_date_scale' => 'date',
        'can_request_meetings' => 'boolean',
        'services_screen_available' => 'boolean',
        'apply_salary_scale' => 'boolean',
        'hiring_date' => 'date',
        'terms_accepted_at' => 'datetime',
        
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'skills' => 'array',

            // تحويل السويتشات والصلاحيات إلى Boolean
            'is_employee' => 'boolean',
            'services_screen_available' => 'boolean',
            'can_view_projects' => 'boolean',
            'can_view_notes' => 'boolean',
            'can_propose_quotes' => 'boolean',
            'can_enter_knowledge_bank' => 'boolean',
            'apply_working_hours' => 'boolean',
            'can_request_meetings' => 'boolean',
            'apply_salary_scale' => 'boolean',
            'is_visible_to_employee' => 'boolean',

            // تحويل التواريخ
            'hiring_date' => 'date',
            'note_date' => 'date',
            'expires_at' => 'datetime',
        ];
    }

    public function request()
    {
        return $this->hasMany(Requests::class);
    }

    public function systems()
    {
        return $this->belongsToMany(System::class, 'partner_system', 'partner_id', 'system_id')
            ->withTimestamps();
    }

    public function getCountryNameAttribute()
    {
        return \App\Support\CountryNames::forCode($this->country);
    }

    public function avatarUrl(): string
    {
        if ($this->avatar) {
            return asset('storage/' . ltrim($this->avatar, '/'));
        }

        // Trainers must upload a photo — never fall back to the site logo.
        if ($this->isTrainer()) {
            return 'data:image/svg+xml,' . rawurlencode(
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80"><rect width="80" height="80" fill="#e5e7eb"/><circle cx="40" cy="30" r="14" fill="#9ca3af"/><path d="M16 70c4-14 14-20 24-20s20 6 24 20" fill="#9ca3af"/></svg>'
            );
        }

        return asset('assets/images/logo.webp');
    }

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function idCardFrontUrl(): ?string
    {
        $path = $this->id_card_front_path ?: $this->id_card_path;
        return $path ? asset('storage/' . ltrim($path, '/')) : null;
    }

    public function idCardBackUrl(): ?string
    {
        return $this->id_card_back_path
            ? asset('storage/' . ltrim($this->id_card_back_path, '/'))
            : null;
    }

    public function isPendingApproval(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Flag image URL for ISO country code (placeholder until dedicated trainer country UX).
     */
    public function countryFlagUrl(): ?string
    {
        $code = strtolower(trim((string) $this->country));
        if (strlen($code) !== 2) {
            return null;
        }

        return 'https://flagcdn.com/w40/' . $code . '.png';
    }

    public function getDisplayNameAttribute(): string
    {
        return SystemManager::nameFor($this);
    }

    public function scopeNotBlocked($query)
    {
        return $query->where(function ($q) {
            $q->where('status', '!=', 'blocked')->orWhereNull('status');
        });
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function getRoleNameAttribute()
    {
        return match ($this->role) {
            'admin' => 'مدير',
            'client' => 'عميل',
            'partner' => 'شريك مطور أنظمة',
            'design_partner' => 'شريك مصمم',
            'advertising_partner' => 'شريك معلن',
            'independent_partner' => 'شريك مستقل',
            'trainer' => 'محاضر',
            'trainee' => 'متدرب',
            default => 'غير محدد',
        };
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }

    public function isTrainee(): bool
    {
        return $this->role === 'trainee';
    }

    /** Modern academy shell (top/bottom nav) instead of main admin dashboard chrome. */
    public function usesAcademyShell(): bool
    {
        return $this->isTrainer() || $this->isTrainee();
    }

    /**
     * Public landing home for this user (trainees stay in the academy flow).
     */
    public function publicHomeRoute(): string
    {
        return $this->isTrainee() ? 'academy.index' : 'system.index';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /** Can enroll in / take courses (learner path). */
    public function canLearnCourses(): bool
    {
        return in_array($this->role, ['admin', 'trainee', 'trainer'], true);
    }

    /** Can manage courses (create/edit/attendance/exams). */
    public function canManageCourses(): bool
    {
        return in_array($this->role, ['admin', 'trainer'], true);
    }

    /** Whether this user manages a specific course. */
    public function managesCourse($course): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->isTrainer()) {
            return false;
        }

        $trainerId = is_object($course) ? ($course->trainer_id ?? null) : null;

        return $trainerId !== null && (int) $trainerId === (int) $this->id;
    }

    public function trainedCourses()
    {
        return $this->hasMany(Course::class, 'trainer_id');
    }

    public function performances()
    {
        return $this->hasMany(Performance::class);
    }

    public function latestPerformance()
    {
        return $this->hasOne(Performance::class)->latestOfMany('performance_date');
    }

    public function earnings()
    {
        return $this->hasMany(Earning::class);
    }

    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function getAvailableBalanceAttribute()
    {
        return $this->earnings()->where('status', 'available')->sum('amount');
    }

    public function getTotalEarningsAttribute()
    {
        return $this->earnings()->sum('amount');
    }

    public function getWithdrawnBalanceAttribute()
    {
        return $this->earnings()->where('status', 'withdrawn')->sum('amount');
    }

    public function assignedRequests()
    {
        return $this->belongsToMany(SpecialRequest::class, 'special_request_partner', 'partner_id', 'special_request_id')
            ->withPivot('status', 'notes', 'assigned_at', 'completed_at')
            ->withTimestamps();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'partner_service', 'partner_id', 'service_id')
            ->withTimestamps();
    }

    public function myServices()
    {
        return $this->hasMany(MyService::class, 'user_id');
    }
    
    public function myService()
    {
        return $this->hasOne(MyServices::class);
    }

    public function project_manager(){
        return $this->hasMany(Project_Manager::class);
    }

    // في ملف User.php
    public function attendedMeetings()
    {
        return $this->belongsToMany(ProjectMeeting::class, 'meeting_participants', 'user_id', 'project_meeting_id')
            ->wherePivot('status', 'attended');
    }

    public function declinedMeetings()
    {
        return $this->belongsToMany(ProjectMeeting::class, 'meeting_participants', 'user_id', 'project_meeting_id')
            ->wherePivot('status', 'declined');
    }

    public function absentMeetings()
    {
        return $this->belongsToMany(ProjectMeeting::class, 'meeting_participants', 'user_id', 'project_meeting_id')
            ->wherePivot('status', 'absent');
    }
    // داخل كلاس User
    public function projectMeetings()
    {
        // نربط المستخدم بالاجتماعات من خلال الجدول الوسيط الخاص بالمشاركين
        return $this->belongsToMany(ProjectMeeting::class, 'meeting_participants', 'user_id', 'project_meeting_id')
            ->withPivot('status');
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user')
            ->withPivot([
                'price_paid',
                'status',
                'enrolled_at',
                'expires_at'
            ])
            ->withTimestamps();
    }

    public function whatsappMessages()
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    public function workTimes()
    {
        return $this->hasMany(WorkTime::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function employeeAdjustments()
    {
        return $this->hasMany(EmployeeAdjustment::class);
    }
}
