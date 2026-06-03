<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestStage extends Model
{
    protected $fillable = ['request_id', 'title', 'details', 'hours_count', 'end_date', 'status'];

    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'request_stage_id');
    }
}
