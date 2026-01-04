<?php
// app/Observers/RequestObserver.php

namespace App\Observers;

use App\Models\Requests;
use App\Models\Support;

class RequestObserver
{
    public function created(Requests $request)
    {
        // إنشاء تذكرة دعم تلقائياً عند إنشاء الطلب
        Support::create([
            'request_id' => $request->id,
            'user_id' => $request->client_id,
            'subject' => $request->order_number,
            'status' => 'open',
            'last_message_at' => now()
        ]);
    }
}
