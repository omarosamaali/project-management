<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Concerns\StoresProjectChatMessage;
use App\Http\Controllers\Controller;
use App\Models\SpecialRequest;
use App\Models\SpecialRequestMessage;
use App\Services\ProjectMessageNotificationService;
use Illuminate\Http\Request;

class SpecialRequestMessageController extends Controller
{
    use StoresProjectChatMessage;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'special_request_id' => 'required|exists:special_requests,id',
            'message' => 'required|string|max:5000',
        ]);

        $message = SpecialRequestMessage::create([
            'special_request_id' => $validated['special_request_id'],
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        return $this->chatStoreResponse(
            $request,
            $message,
            $validated['message'],
            function (int $senderId, string $senderName, string $preview) use ($validated) {
                $project = SpecialRequest::find($validated['special_request_id']);
                if ($project) {
                    app(ProjectMessageNotificationService::class)
                        ->notifySpecialRequest($project, $senderId, $senderName, $preview);
                }
            }
        );
    }
}
