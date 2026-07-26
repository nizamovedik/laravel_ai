<?php

namespace App\Actions\Task;

use App\DTO\TaskData;
use App\Models\Task;
use App\Repositories\TaskLabelRepository;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateTaskAction
{
    public function __construct(
        private TaskRepository $taskRepository,
        private TaskLabelRepository $taskLabelRepository
    ) {}

    public function execute(Task $task, TaskData $data, int $userId): Task
    {
        $updateData = array_filter([
            'title' => $data->title,
            'description' => $data->description,
            'priority_id' => $data->priorityId,
            'assignee_id' => $data->assigneeId,
            'deadline_at' => $data->deadlineAt,
            'estimated_hours' => $data->estimatedHours,
        ], fn ($value) => ! is_null($value));

        $this->taskRepository->update($task, $updateData);

        if (! empty($data->labelIds)) {
            $this->taskLabelRepository->syncLabels($task, $data->labelIds);
        }

        Cache::forget("project_tasks_{$task->project_id}");
        Log::info("Задача {$task->id} обновлена пользователем {$userId}");

        return $task->fresh();
    }
}
