<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SpecialRequestMessage;
use App\Models\SpecialRequest;
use App\Services\WhatsAppOTPService;
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

        try {
            $project = SpecialRequest::find($request->special_request_id);
            if ($project) {
                $whatsapp = app(WhatsAppOTPService::class);
                $senderName = auth()->user()->name;
                $preview = mb_substr($request->message, 0, 60);
                foreach ($project->partners()->get() as $member) {
                    if ($member->phone && $member->id !== auth()->id()) {
                        $whatsapp->sendProjectNotification($member->phone, $member->name, "رسالة جديدة من {$senderName}: \"{$preview}\"", $project->title);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("[MSG_NOTIFY] " . $e->getMessage());
        }

        return back()->with('success', 'تم إرسال رسالتك بنجاح');
    }
    
}