<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectStage extends Model
{
    protected $fillable = ['special_request_id', 'title', 'details', 'end_date', 'status'];

    // علاقة مع المهام
    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_stage_id');
    }

    // حساب عدد المهام في المرحلة
    public function getTasksCountAttribute()
    {
        return $this->tasks()->count();
    }

    // حساب عدد المهام المنجزة
    public function getCompletedTasksCountAttribute()
    {
        return $this->tasks()->where('status', 'completed')->count();
    }

    // حساب نسبة الإنجاز
    public function getCompletionPercentageAttribute()
    {
        $totalTasks = $this->tasks_count;
        if ($totalTasks == 0) {
            return 0;
        }
        $completedTasks = $this->completed_tasks_count;
        return round(($completedTasks / $totalTasks) * 100, 2);
    }
}
