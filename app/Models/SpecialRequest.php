<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RequestFile;

class SpecialRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'project_type',
        'description',
        'core_features',
        'examples',
        'budget',
        'deadline',
        'status',
        'is_project',
        'price',
        'payment_type',
        'installments_data',
        'bidding_deadline',
        'order_number',
    ];

    protected $casts = [
        'installments_data' => 'array',
        'is_project' => 'boolean',
    ];

    public function files()
    {
        return $this->hasMany(RequestFile::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusNameAttribute()
    {
        return match ($this->status) {
            'pending' => 'قيد الانتظار',
            'in_review' => 'قيد المراجعة',
            'in_progress' => 'قيد المعالجة',
            'completed' => 'مكتمل',
            'canceled' => 'ملغية',
            'بانتظار الدفع' => 'بانتظار الدفع',
        };
    }

    public function partners()
    {
        return $this->belongsToMany(
            User::class,
            'special_request_partner',
            'special_request_id',
            'partner_id'
        )->withPivot('notes', 'created_at', 'profit_share_percentage')->withTimestamps();
    }

    public function assignedPartner()
    {
        return $this->belongsToMany(User::class, 'special_request_partner', 'special_request_id', 'partner_id')
            ->wherePivot('status', '!=', 'rejected')
            ->withPivot('status', 'notes', 'assigned_at', 'completed_at')
            ->withTimestamps()
            ->latest('special_request_partner.created_at');
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function payments()
    {
        return $this->hasMany(SpecialRequestPayment::class, 'special_request_id');
    }

    public function requestPayments()
    {
        return $this->hasMany(RequestPayment::class);
    }


    public function getTotalPaidAmount()
    {
        return $this->payments()->where('status', 'paid')->sum('amount');
    }

    public function getRemainingAmount()
    {
        return $this->price - $this->getTotalPaidAmount();
    }

    public function isFullyPaid()
    {
        return $this->getTotalPaidAmount() >= $this->price;
    }

    public function getPaymentProgress()
    {
        if (!$this->price) return 0;
        return ($this->getTotalPaidAmount() / $this->price) * 100;
    }

    // Helper method لعرض نوع الدفع
    public function getPaymentTypeNameAttribute()
    {
        return $this->payment_type === 'single' ? 'دفعة واحدة' : 'دفعات (تقسيط)';
    }

    public function stages()
    {
        return $this->hasMany(ProjectStage::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'special_request_id');
    }

    public function notes()
    {
        return $this->hasMany(ProjectNote::class)->orderBy('created_at', 'desc');
    }

    public function expenses(){
    return $this->hasMany(Expenses::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function projectFiles()
    {
        return $this->hasMany(ProjectFile::class, 'special_request_id');
    }

    public function activities()
    {
        return $this->hasMany(ProjectActivity::class)->latest();
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function projectManager()
    {
        return $this->hasOne(Project_Manager::class);
    }
    public function projectMeetings()
    {
        return $this->hasMany(ProjectMeeting::class, 'special_request_id');
    }

    public function proposals()
    {
        return $this->hasMany(ProjectProposal::class, 'project_id');
    }
}
