<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait StoresProjectChatMessage
{
    protected function jsonChatMessage(Model $message, string $body): JsonResponse
    {
        $message->loadMissing('user');
        $user = $message->user ?? auth()->user();

        return response()->json([
            'message' => [
                'id' => $message->id,
                'message' => $body,
                'user_id' => $message->user_id,
                'user_name' => $user?->name ?? 'مستخدم',
                'user_image' => $user?->image ? asset('storage/'.$user->image) : null,
                'user_initial' => mb_substr($user?->name ?? 'U', 0, 1),
                'created_at_human' => $message->created_at?->diffForHumans() ?? 'الآن',
                'is_mine' => (int) $message->user_id === (int) auth()->id(),
            ],
        ]);
    }

    protected function chatStoreResponse(
        Request $request,
        Model $message,
        string $body,
        callable $afterSave,
        string $flashMessage = 'تم إرسال رسالتك بنجاح'
    ): RedirectResponse|JsonResponse {
        $user = auth()->user();
        $preview = mb_substr($body, 0, 60);

        dispatch(function () use ($afterSave, $user, $preview) {
            $afterSave($user->id, $user->name, $preview);
        })->afterResponse();

        if ($request->expectsJson()) {
            return $this->jsonChatMessage($message, $body);
        }

        return back()->with('success', $flashMessage);
    }
}
