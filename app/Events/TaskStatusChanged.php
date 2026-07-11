<?php

namespace App\Events;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskStatusChanged
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public readonly Task $task,
        public readonly TaskStatusEnum $oldStatus,
        public readonly TaskStatusEnum $newStatus,
        public readonly int $changedByUserId) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
