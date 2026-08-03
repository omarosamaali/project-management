<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CourseSpecialCertificate extends Model
{
    protected $fillable = [
        'course_id',
        'payment_id',
        'user_id',
        'uploaded_by',
        'file_path',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function fileUrl(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }
}
