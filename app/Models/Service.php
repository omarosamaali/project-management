<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'image',
        'name_ar',
        'name_en',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusNameAttribute()
    {
        return match ($this->status) {
            'web' => 'موقع ويب (Web Application)',
            'mobile' => 'تطبيق موبايل (Mobile App)',
            'both' => 'كلاهما (Web & Mobile)',
            'logo' => 'تصميم شعار (Logo)',
            'identity' => 'تصميم هوية مؤسسة (Business Identity)',
            'digital' => 'تسويق إلكتروني (Digital Marketing)',
            'management' => 'إدارة موقع او تطبيق (Website Management)',
            'social' => 'إدارة حساب التواصل الاجتماعي (Social Media Management)',
            'training' => 'دورة تدريبية (Training)',
            'consulting' => 'استشارات تقنية (Technical Consulting)',
            'other' => 'طلب اخر (Other)',
            'desktop' => 'موقع سطح المكتب (Desktop Application)',
            default => 'غير محدد (Not Specified)',
        };
    }

    public function systems()
    {
        return $this->hasMany(System::class);
    }

    public function myServices() {
        return $this->belongsToMany(MyService::class);
    }
}
