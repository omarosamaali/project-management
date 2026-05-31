<?php

namespace App\Http\Controllers;

use App\Models\RequestMessage;
use App\Models\Requests;
use App\Services\WhatsAppOTPService;
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

        try {
            $project = Requests::find($validated['request_id']);
            if ($project) {
                $whatsapp = app(WhatsAppOTPService::class);
                $senderName = auth()->user()->name;
                $preview = mb_substr($request->message, 0, 60);
                foreach ($project->partners()->get() as $member) {
                    if ($member->phone && $member->id !== auth()->id()) {
                        $whatsapp->sendProjectNotification($member->phone, $member->name, "رسالة جديدة من {$senderName}: \"{$preview}\"", $project->title ?? "طلب #{$project->id}");
                    }
                }
                $whatsapp->notifyManager("رسالة جديدة من {$senderName}: \"{$preview}\"", $project->title ?? "طلب #{$project->id}");
            }
        } catch (\Exception $e) {
            \Log::error("[MSG_NOTIFY] " . $e->getMessage());
        }

        return back()->with('success', 'تم إرسال الرسالة بنجاح');
    }
}
