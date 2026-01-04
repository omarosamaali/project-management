<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestsIssue extends Model
{
protected $table = 'requests_issues';
protected $fillable = ['request_id', 'user_id', 'title', 'description', 'image', 'assigned_users', 'status'];
protected $casts = ['assigned_users' => 'array'];

public function user() {
return $this->belongsTo(User::class);
}
}