<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SpecialRequestMessage;
use Illuminate\Http\Request;

class SpecialRequestMessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'special_request_id' => 'required|exists:special_requests,id',
            'message' => 'required|string|max:5000',
        ]);

        SpecialRequestMessage::create([
            'special_request_id' => $request->special_request_id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return back()->with('success', 'تم إرسال رسالتك بنجاح');
    }
    
}