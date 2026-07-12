<?php

namespace App\Services;

use App\DTO\TaskData;
use App\Enums\TaskStatusEnum;
use App\Events\TaskStatusChanged;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class TaskService
{
    public function __construct(
        private TaskRepository $taskRepository
    ) {}

    public function createTask(TaskData $data): Task
    {
        return DB::transaction(function () use ($data) {
            // Создаём задачу всегда со статусом "Новая"
            $task = $this->taskRepository->create(
                array_merge($data->toArray(), [
                    'status' => TaskStatusEnum::NEW,
                ])
            );

            Log::info("Задача {$task->id} создана пользователем {$data->creatorId}");

            return $task;
        });
    }

    /**
     * Универсальный метод для смены статуса
     */
    public function changeStatus(Task $task, TaskStatusEnum $newStatus, int $changedByUserId): bool
    {
        $currentStatus = $task->status;

        if (! $currentStatus instanceof TaskStatusEnum) {
            throw new InvalidArgumentException('Некорректный статус задачи');
        }

        // Проверяем возможность перехода
        if (! $currentStatus->canTransitionTo($newStatus)) {
            throw new InvalidArgumentException(
                "Невозможно перевести задачу из статуса '{$currentStatus->label()}' в '{$newStatus->label()}'"
            );
        }

        return DB::transaction(function () use ($task, $newStatus, $currentStatus, $changedByUserId) {
            // Обновляем статус
            $this->taskRepository->updateStatus($task, $newStatus->value);

            // Дополнительная логика при переходах
            match ($newStatus) {
                TaskStatusEnum::IN_PROGRESS => $this->taskRepository->markAsStarted($task),
                TaskStatusEnum::DONE, TaskStatusEnum::CLOSED => $this->taskRepository->markAsCompleted($task),
                default => null,
            };

            // Генерируем событие
            event(new TaskStatusChanged(
                task: $task,
                oldStatus: $currentStatus,
                newStatus: $newStatus,
                changedByUserId: $changedByUserId
            ));

            return true;
        });
    }

    // Упрощённые методы-обёртки (для удобства вызова из контроллеров)
    public function startTask(Task $task, int $userId): bool
    {
        return $this->changeStatus($task, TaskStatusEnum::IN_PROGRESS, $userId);
    }

    public function completeTask(Task $task, int $userId): bool
    {
        return $this->changeStatus($task, TaskStatusEnum::CLOSED, $userId);
    }

    public function reopenTask(Task $task, int $userId): bool
    {
        return $this->changeStatus($task, TaskStatusEnum::IN_PROGRESS, $userId);
    }

    public function assignTask(Task $task, ?int $assigneeId): bool
    {
        return $this->taskRepository->setAssignee($task, $assigneeId);
    }

    public function updateTask(Task $task, TaskData $data, int $userId): Task
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

        // Логируем обновление
        Log::info("Задача {$task->id} обновлена пользователем {$userId}");

        return $task->fresh();
    }

    public function deleteTask(Task $task): void
    {
        $this->taskRepository->delete($task);
    }

    public function syncLabels(Task $task, array $labelIds): void
    {
        $this->taskRepository->syncLabels($task, $labelIds);
    }

    public function getFilteredTasks(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->taskRepository->getFilteredTasks($filters, $perPage);
    }
}
