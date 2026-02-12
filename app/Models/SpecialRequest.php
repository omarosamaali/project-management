<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RequestFile;
use Carbon\Carbon;
use App\Models\RequestPrice;

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

    // داخل ملف App\Models\SpecialRequest.php

    public function requestFiles()
    {
        // تأكد أن الاسم هنا مطابق لما تستدعيه في الـ Blade
        return $this->hasMany(ProjectFile::class, 'special_request_id');
    }

    protected $casts = [
        'installments_data' => 'array',
        'is_project' => 'boolean',
        'deadline' => 'datetime',           // ✅ مهم جداً
        'bidding_deadline' => 'datetime',   // ✅ مهم جداً
        'created_at' => 'datetime',         // ✅ مهم جداً
        'updated_at' => 'datetime',         // ✅ مهم جداً

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
            'pending' => 'جديد',
            'in_review' => 'قيد المراجعة',
            'in_progress' => 'قيد المعالجة',
            'completed' => 'مكتمل',
            'canceled' => 'ملغية',
            'active' => 'جاري العمل به',
            'بانتظار الدفع' => 'بانتظار الدفع',
            'بانتظار عروض الاسعار' => 'بانتظار عروض الاسعار',
        };
    }

    public function partners()
    {
        return $this->belongsToMany(
            User::class,
            'special_request_partner',
            'special_request_id',
            'partner_id'
        )->withPivot('notes', 'created_at', 'profit_share_percentage',
            'share_type',       // ← لازم يكون موجود
            'fixed_amount',     // ← لازم يكون موجود
        )->withTimestamps();
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

    public function messages()
    {
        return $this->hasMany(SpecialRequestMessage::class, 'special_request_id');
    }


    // ✅ حساب الساعات المتوقعة من الفرق بين deadline و created_at
    public function getExpectedHoursAttribute()
    {
        if ($this->deadline && $this->created_at) {
            $start = Carbon::parse($this->created_at);
            $end = Carbon::parse($this->deadline);

            // حساب الفرق بالساعات
            return $start->diffInHours($end);
        }
        return 0;
    }

    // ✅ حساب الساعات المستغرقة حتى الآن من created_at إلى الآن
    public function getSpentHoursAttribute()
    {
        if ($this->created_at) {
            $start = Carbon::parse($this->created_at);
            $now = Carbon::now();

            // حساب الفرق بالساعات من البداية حتى الآن
            $hoursSpent = $start->diffInHours($now);

            // إذا كان المشروع مكتمل، نحسب من البداية حتى تاريخ التحديث
            if (in_array($this->status, ['completed', 'canceled'])) {
                $end = Carbon::parse($this->updated_at);
                return $start->diffInHours($end);
            }

            return $hoursSpent;
        }
        return 0;
    }

    // ✅ حساب نسبة الإنجاز
    public function getProgressPercentageAttribute()
    {
        if ($this->expected_hours > 0) {
            return min(round(($this->spent_hours / $this->expected_hours) * 100, 1), 100);
        }
        return 0;
    }

    // ✅ الساعات المتبقية
    public function getRemainingHoursAttribute()
    {
        $remaining = $this->expected_hours - $this->spent_hours;
        return max($remaining, 0);
    }

    // ✅ حالة المشروع (متأخر أم لا)
    public function getIsOverdueAttribute()
    {
        if ($this->deadline && $this->status != 'completed') {
            return Carbon::now()->isAfter($this->deadline);
        }
        return false;
    }

    // ✅ عدد الأيام المتبقية
    public function getRemainingDaysAttribute()
    {
        if ($this->deadline && $this->status != 'completed') {
            $now = Carbon::now();
            $deadline = Carbon::parse($this->deadline);

            if ($deadline->isFuture()) {
                return $now->diffInDays($deadline);
            }
            return 0;
        }
        return 0;
    }
}
