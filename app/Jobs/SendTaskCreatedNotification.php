<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendTaskCreatedNotification implements ShouldQueue
{
    use Queueable;

    public Task $task;

    /**
     * Create a new job instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("📧 Уведомление: создана задача '{$this->task->title}'", [
            'task_id' => $this->task->id,
            'assignee_id' => $this->task->assignee_id,
            'created_by' => $this->task->creator_id,
        ]);
    }
}
