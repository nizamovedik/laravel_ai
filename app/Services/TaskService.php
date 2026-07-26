<?php

namespace App\Services;

use App\Actions\Task\ChangeTaskStatusAction;
use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\DeleteTaskAction;
use App\Actions\Task\UpdateTaskAction;
use App\DTO\TaskData;
use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

class TaskService
{
    public function __construct(
        private TaskRepository $taskRepository,
        private CreateTaskAction $createTaskAction,
        private UpdateTaskAction $updateTaskAction,
        private DeleteTaskAction $deleteTaskAction,
        private ChangeTaskStatusAction $changeTaskStatusAction,
    ) {}

    public function createTask(TaskData $data): Task
    {
        return $this->createTaskAction->execute($data);
    }

    public function updateTask(Task $task, TaskData $data, int $userId): Task
    {
        return $this->updateTaskAction->execute($task, $data, $userId);
    }

    public function deleteTask(Task $task): void
    {
        $this->deleteTaskAction->execute($task);
    }

    public function changeStatus(Task $task, string $newStatus, int $changedByUserId): bool
    {
        $newStatusEnum = TaskStatusEnum::tryFrom($newStatus);

        if (! $newStatusEnum) {
            throw new InvalidArgumentException('Некорректный статус');
        }

        return $this->changeTaskStatusAction->execute($task, $newStatusEnum, $changedByUserId);
    }

    public function getFilteredTasks(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->taskRepository->getFilteredTasks($filters, $perPage);
    }
}
