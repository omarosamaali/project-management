<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseChatLockToggled implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $courseId,
        public bool $locked,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('course.'.$this->courseId.'.chat')];
    }

    public function broadcastAs(): string
    {
        return 'chat.lock.toggled';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return ['chat_locked' => $this->locked];
    }
}
