<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseChatUserModerationChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $courseId,
        public int $userId,
        public bool $blocked,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('course.'.$this->courseId.'.chat')];
    }

    public function broadcastAs(): string
    {
        return 'chat.user.moderation';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'blocked' => $this->blocked,
        ];
    }
}
