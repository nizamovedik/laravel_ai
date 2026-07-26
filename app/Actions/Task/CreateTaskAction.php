<?php

namespace App\Actions\Task;

use App\DTO\TaskData;
use App\Enums\TaskStatusEnum;
use App\Jobs\SendTaskCreatedNotification;
use App\Models\Task;
use App\Repositories\TaskLabelRepository;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CreateTaskAction
{
    public function __construct(
        private TaskRepository $taskRepository,
        private TaskLabelRepository $taskLabelRepository
    ) {}

    public function execute(TaskData $data): Task
    {
        return DB::transaction(function () use ($data) {
            $task = $this->taskRepository->create(
                array_merge($data->toArray(), [
                    'status' => TaskStatusEnum::NEW,
                ])
            );

            if (! empty($data->labelIds)) {
                $this->taskLabelRepository->syncLabels($task, $data->labelIds);
            }

            Cache::forget("project_tasks_{$task->project_id}");
            Redis::incr("project:{$task->project_id}:tasks_count");
            SendTaskCreatedNotification::dispatch($task);

            Log::info("Задача {$task->id} создана пользователем {$data->creatorId}");

            return $task;
        });
    }
}
