<?php

namespace App\Listeners;

use App\Events\TaskStatusChanged;
use App\Models\TaskHistory;

class LogTaskHistory
{
    public function handle(TaskStatusChanged $event): void
    {
        TaskHistory::create([
            'task_id' => $event->task->id,
            'user_id' => $event->changedByUserId,
            'field' => 'status',
            'old_value' => $event->oldStatus->label(),
            'new_value' => $event->newStatus->label(),
        ]);
    }
}
