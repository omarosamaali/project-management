<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\StoresProjectChatMessage;
use App\Models\RequestMessage;
use App\Models\Requests;
use App\Services\ProjectMessageNotificationService;
use Illuminate\Http\Request;

class RequestMessageController extends Controller
{
    use StoresProjectChatMessage;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = RequestMessage::create([
            'request_id' => $validated['request_id'],
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        return $this->chatStoreResponse(
            $request,
            $message,
            $validated['message'],
            function (int $senderId, string $senderName, string $preview) use ($validated) {
                $project = Requests::find($validated['request_id']);
                if ($project) {
                    app(ProjectMessageNotificationService::class)
                        ->notifyRequest($project, $senderId, $senderName, $preview);
                }
            },
            'تم إرسال الرسالة بنجاح'
        );
    }
}
