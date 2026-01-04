<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $table = 'knowledge_base';
    protected $fillable = ['category_id', 'title', 'details', 'attachments', 'added_by'];

    public function category()
    {
        return $this->belongsTo(KbCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
