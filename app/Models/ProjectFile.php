<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFile extends Model
{
    protected $fillable = ['special_request_id', 'user_id', 'title', 'description', 'file_path', 'file_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialRequest()
    {
        return $this->belongsTo(SpecialRequest::class);
    }
}
