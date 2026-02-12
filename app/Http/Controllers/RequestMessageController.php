<?php

namespace App\Http\Controllers;

use App\Models\RequestMessage;
use Illuminate\Http\Request;

class RequestMessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
            'message' => 'required|string|max:1000'
        ]);

        RequestMessage::create([
            'request_id' => $validated['request_id'],
            'user_id' => auth()->id(),
            'message' => $validated['message']
        ]);

        return back()->with('success', 'تم إرسال الرسالة بنجاح');
    }
}
