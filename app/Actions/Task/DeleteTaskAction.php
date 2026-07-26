<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class DeleteTaskAction
{
    public function __construct(
        private TaskRepository $taskRepository
    ) {}

    public function execute(Task $task): void
    {
        $projectId = $task->project_id;

        $this->taskRepository->delete($task);
        Redis::decr("project:{$projectId}:tasks_count");
        Cache::forget("project_tasks_{$projectId}");
    }
}
