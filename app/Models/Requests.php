<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requests extends Model
{
    protected $table = 'requests'; // ✅ هذا هو الحل

    protected $fillable = [
        'order_number',
        'system_id',
        'client_id',
        'status',
    ];


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
        // أضفنا اسم الجدول 'special_request_partner' كمعامل ثاني
        return $this->belongsToMany(User::class, 'special_request_partner', 'request_id', 'partner_id')
            ->withPivot('status', 'notes', 'profit_share_percentage', 'created_at') // تأكد من إضافة الحقول هنا لتعمل في pivot
            ->withTimestamps();
    }

    public function client() {
        return $this->belongsTo(User::class, 'client_id');
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
        return $this->hasManyThrough(Task::class, RequestStage::class, 'request_id', 'request_stage_id');
    }

    public function notes()
    {
        return $this->hasMany(RequestNote::class, 'request_id');
    }
    // إضافة العلاقة للدفعات
    public function requestPayments()
    {
        return $this->hasMany(RequestsPayment::class, 'request_id');
    }

    public function expenses()
    {
        return $this->hasMany(RequestsExpense::class, 'request_id');
    }

    public function requestFiles() // تأكد من الاسم لاستخدامه في الـ Blade
    {
        return $this->hasMany(RequestFile::class, 'request_id');
    }

    // داخل كلاس Request
    public function activities()
    {
        // لاحظ الربط بـ RequestActivity والـ foreign_key
        return $this->hasMany(RequestActivity::class, 'request_id')->latest();
    }

    // داخل كلاس Request العادي
    public function projectMeetings()
    {
        // سميتها projectMeetings لتعمل مع نفس كود الـ Blade الذي نسخته
        return $this->hasMany(RequestMeeting::class, 'request_id');
    }

    // داخل موديل Request
    public function proposals()
    {
        return $this->hasMany(RequestProposal::class, 'request_id');
    }

    public function payments()
    {
        return $this->hasMany(RequestsPayment::class, 'request_id');
    }

    // داخل موديل Requests
    public function projectManager()
    {
        // الطلب الواحد له مدير مشروع واحد
        return $this->hasOne(Project_Manager::class, 'request_id');
    }
}