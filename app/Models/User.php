<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\WithdrawalRequest;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
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

        // أضف هذه الحقول الجديدة هنا:
        'note_title',
        'note_date',
        'note_details',
        'note_attachment',
        'is_visible_to_employee',

        'apply_salary_scale',
        'salary_year',
        'salary_month',
        'salary_attachment',
        'salary_notes',
        'salary_amount',
        'salary_currency',
        'hiring_date'
    ];

    protected $casts = [
        'is_employee' => 'boolean',
        'can_view_projects' => 'boolean',
        'can_view_notes' => 'boolean',
        'can_propose_quotes' => 'boolean',
        'can_enter_knowledge_bank' => 'boolean',
        'apply_working_hours' => 'boolean',
        'can_request_meetings' => 'boolean',
        'services_screen_available' => 'boolean',
        'apply_salary_scale' => 'boolean',
        'hiring_date' => 'date',
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
        if (!$this->country) {
            return null;
        }
        $cacheKey = 'country_names_ar';
        $countries = cache()->remember($cacheKey, now()->addMonth(), function () {
            try {
                $response = file_get_contents('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
                $data = json_decode($response, true);
                $list = [];
                foreach ($data as $country) {
                    $code = $country['cca2'];
                    $name = $country['translations']['ara']['common'] ?? $country['name']['common'];
                    $list[$code] = $name;
                }
                return $list;
            } catch (\Exception $e) {
                return [];
            }
        });
        return $countries[strtoupper($this->country)] ?? $this->country;
    }

    public function getRoleNameAttribute()
    {
        return match ($this->role) {
            'admin' => 'مدير',
            'client' => 'عميل',
            'partner' => 'شريك مطور أنظمة',
            'design_partner' => 'شريك مصمم',
            'advertising_partner' => 'شريك معلن',
            default => 'غير محدد',
        };
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
        return $this->belongsToMany(MyService::class);
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

    
}
