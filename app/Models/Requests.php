<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use App\Models\Concerns\HasMaintenanceSupport;
use App\Models\Concerns\HasProjectClients;
use App\Models\Concerns\HasProjectWorkTimeMetrics;

class Requests extends Model
{
    use HasMaintenanceSupport, HasProjectClients, HasProjectWorkTimeMetrics;

    protected function projectOwnerColumn(): string
    {
        return 'client_id';
    }
    protected $table = 'requests';

    protected $fillable = [
        'order_number',
        'system_id',
        'client_id',
        'status',
        'delivered_at',
        'maintenance_period',
        'maintenance_unit',
    ];
    protected $casts = [
        'delivered_at' => 'datetime',
    ];
    public function technicalSupports()
    {
        return $this->hasMany(TechnicalSupport::class, 'request_id');
    }



    public function issues()
    {
        return $this->hasMany(RequestsIssue::class, 'request_id');
    }

    public function system(){
        return $this->belongsTo(System::class);
    }

    // app/Models/Requests.php

    public function partners()
    {
        return $this->belongsToMany(User::class, 'special_request_partner', 'request_id', 'partner_id')
            ->where(function ($q) {
                $q->where('users.status', '!=', 'blocked')->orWhereNull('users.status');
            })
            ->withPivot('status', 'notes', 'profit_share_percentage', 'created_at')
            ->withTimestamps();
    }

    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function clients()
    {
        return $this->belongsToMany(User::class, 'request_clients', 'request_id', 'user_id')
                    ->withTimestamps();
    }

    public function installments()
    {
        // تأكد من اسم الموديل واسم المفتاح الأجنبي
        return $this->hasMany(RequestPrice::class, 'request_id');
    }

    public function getPriceAttribute()
    {
        return $this->installments()->sum('amount') ?? 0;
    }

    public function getInstallmentsCountAttribute()
    {
        return $this->installments()->count();
    }
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'new' => 'جديد',
            'in_progress' => 'تحت الاجراء',
            'waiting_client' => 'بإنتظار رد العميل',
            'pending' => 'بالانتظار',
            'closed' => 'ملغية',
            'suspended' => 'معلقة',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function support()
    {
        return $this->hasOne(Support::class, 'request_id');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class, 'request_id');
    }

    public function stages()
    {
        return $this->hasMany(RequestStage::class, 'request_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'request_id');
    }

    public function notes()
    {
        return $this->hasMany(RequestNote::class, 'request_id');
    }

    public function requestPayments()
    {
        return $this->hasMany(RequestsPayment::class, 'request_id');
    }

    public function expenses()
    {
        return $this->hasMany(RequestsExpense::class, 'request_id');
    }

    public function requestFiles()
    {
        return $this->hasMany(RequestFile::class, 'request_id');
    }

    public function activities()
    {
        return $this->hasMany(RequestActivity::class, 'request_id')->latest();
    }

    public function projectMeetings()
    {
        return $this->hasMany(ProjectMeeting::class, 'request_id');
    }

    public function proposals()
    {
        return $this->hasMany(RequestProposal::class, 'request_id');
    }

    public function payments()
    {
        return $this->hasMany(RequestsPayment::class, 'request_id');
    }

    public function projectManager()
    {
        return $this->hasOne(Project_Manager::class, 'request_id');
    }

    public function messages()
    {
        return $this->hasMany(RequestMessage::class, 'request_id');
    }

    public function getSupportStartDateAttribute(): ?Carbon
    {
        if (!$this->delivered_at) return null;

        // تأكد إن delivered_at هو Carbon وليس string
        return $this->delivered_at instanceof Carbon
            ? $this->delivered_at
            : Carbon::parse($this->delivered_at);
    }

    public function getSupportEndDateAttribute(): ?Carbon
    {
        if ($this->usesProjectMaintenance()) {
            return $this->getMaintenanceEndDate();
        }

        $start = $this->support_start_date;
        $days  = (int) ($this->system?->support_days ?? 0);

        if (!$start || $days <= 0) {
            return null;
        }

        return $start->copy()->addDays($days);
    }

    public function getProjectDisplayNameAttribute(): string
    {
        return $this->system?->name_ar ?? ('مشروع #' . $this->id);
    }

    public function getSupportTotalDaysAttribute(): int
    {
        if ($this->usesProjectMaintenance()) {
            return $this->maintenance_total_days;
        }

        return (int) ($this->system?->support_days ?? 0);
    }

    /**
     * الأيام المتبقية للدعم (صيانة المشروع بعد التسليم أو أيام النظام)
     */
    public function getSupportRemainingDaysAttribute(): ?int
    {
        if ($this->usesProjectMaintenance()) {
            return $this->maintenance_remaining_days;
        }

        $total = (int) ($this->system?->support_days ?? 0);

        if ($total <= 0 || !$this->delivered_at) {
            return null;
        }

        $startDate = $this->delivered_at instanceof Carbon
            ? $this->delivered_at
            : Carbon::parse($this->delivered_at);

        $passed = (int) $startDate->copy()->startOfDay()
            ->diffInDays(now()->startOfDay(), false);

        return max(0, $total - $passed);
    }

    public function getHasActiveSupportAttribute(): bool
    {
        if (!$this->delivered_at || $this->status !== 'closed') {
            return false;
        }

        if ($this->usesProjectMaintenance()) {
            return $this->has_active_maintenance;
        }

        $r = $this->support_remaining_days;

        return $r !== null && $r > 0;
    }

    public function getSupportPercentageAttribute(): int
    {
        if ($this->usesProjectMaintenance()) {
            return $this->maintenance_percentage;
        }

        $total = (int) ($this->system?->support_days ?? 0);
        $r     = $this->support_remaining_days;

        if ($total <= 0 || $r === null || $r <= 0) {
            return 0;
        }

        return (int) min(100, ($r / $total) * 100);
    }

    public function getSupportColorAttribute(): string
    {
        if ($this->usesProjectMaintenance()) {
            return $this->maintenance_color;
        }

        $r     = $this->support_remaining_days;
        $total = (int) ($this->system?->support_days ?? 1);

        if ($r === null || $r <= 0 || $total <= 0) {
            return 'gray';
        }

        $pct = ($r / $total) * 100;

        if ($pct > 50) {
            return 'green';
        }
        if ($pct > 20) {
            return 'yellow';
        }

        return 'red';
    }
}