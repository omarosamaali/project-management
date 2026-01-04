<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectActivity extends Model
{
    use HasFactory;

    // الحقول المسموح بتعبئتها
    protected $fillable = [
        'special_request_id',
        'user_id',
        'type',
        'description',
        'properties'
    ];

    // تحويل حقل properties من JSON إلى مصفوفة تلقائياً
    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * العلاقة مع الطلب الخاص (المشروع)
     */
    public function specialRequest()
    {
        return $this->belongsTo(SpecialRequest::class, 'special_request_id');
    }

    /**
     * العلاقة مع المستخدم الذي قام بالنشاط
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
