<?php

namespace App\Actions\Task;

use App\Enums\TaskStatusEnum;
use App\Events\TaskStatusChanged;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ChangeTaskStatusAction
{
    public function __construct(
        private TaskRepository $taskRepository
    ) {}

    public function execute(Task $task, TaskStatusEnum $newStatus, int $changedByUserId): bool
    {
        $currentStatus = $task->status;

        if (! $currentStatus instanceof TaskStatusEnum) {
            throw new InvalidArgumentException('Некорректный статус задачи');
        }

        if (! $currentStatus->canTransitionTo($newStatus)) {
            throw new InvalidArgumentException(
                "Невозможно перевести задачу из статуса '{$currentStatus->label()}' в '{$newStatus->label()}'"
            );
        }

        return DB::transaction(function () use ($task, $newStatus, $currentStatus, $changedByUserId) {
            $this->taskRepository->updateStatus($task, $newStatus->value);

            Cache::forget("project_tasks_{$task->project_id}");

            match ($newStatus) {
                TaskStatusEnum::IN_PROGRESS => $this->taskRepository->markAsStarted($task),
                TaskStatusEnum::DONE, TaskStatusEnum::CLOSED => $this->taskRepository->markAsCompleted($task),
                default => null,
            };

            event(new TaskStatusChanged(
                task: $task,
                oldStatus: $currentStatus,
                newStatus: $newStatus,
                changedByUserId: $changedByUserId
            ));

            return true;
        });
    }
}
