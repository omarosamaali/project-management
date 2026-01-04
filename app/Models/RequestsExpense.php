<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestsExpense extends Model
{
// حدد اسم الجدول يدوياً لأن لارافل قد لا يخمن الجمع المزدوج بشكل صحيح
protected $table = 'requests_expenses';

protected $fillable = ['request_id', 'user_id', 'title', 'price', 'details', 'attachment'];

public function user() {
return $this->belongsTo(User::class);
}
}