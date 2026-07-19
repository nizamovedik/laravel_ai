<?php

namespace App\Repositories;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TaskRepository
{
    public function create(array $data): Task
    {
        $data['status'] = $data['status'] ?? TaskStatusEnum::NEW->value;

        return Task::create($data);
    }

    public function findById(int $id): ?Task
    {
        return Task::find($id);
    }

    public function getTasksByProject(int $projectId): Collection
    {
        return Task::where('project_id', $projectId)->get();
    }

    public function getTasksByAssignee(int $assigneeId): Collection
    {
        return Task::where('assignee_id', $assigneeId)->get();
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function updateStatus(Task $task, string $status): bool
    {
        return $task->update(['status' => $status]);
    }

    public function setAssignee(Task $task, ?int $assigneeId): bool
    {
        return $task->update(['assignee_id' => $assigneeId]);
    }

    public function markAsStarted(Task $task): bool
    {
        if (is_null($task->started_at)) {
            return $task->update(['started_at' => now()]);
        }

        return true;
    }

    public function markAsCompleted(Task $task): bool
    {
        return $task->update([
            'completed_at' => now(),
        ]);
    }

    public function delete(Task $task): void
    {
        $task->delete($task);
    }

    public function attachLabels(Task $task, array $labelIds): void
    {
        $task->labels()->attach($labelIds);
    }

    public function detachLabels(Task $task, array $labelIds): void
    {
        $task->labels()->detach($labelIds);
    }

    public function getFilteredTasks(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = Task::query()
            ->with(['priority', 'creator', 'assignee', 'project', 'labels']);

        // Фильтр по статусу
        // if (! empty($filters['status_id'])) {
        //     $query->where('status', $filters['status_id']);
        // }

        // Фильтр по исполнителю
        if (! empty($filters['assignee_id'])) {
            $query->where('assignee_id', $filters['assignee_id']);
        }

        // Фильтр по проекту
        if (! empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        // Фильтр по тегу
        if (! empty($filters['label_id'])) {
            $query->whereHas('labels', function ($q) use ($filters) {
                $q->where('task_labels.id', $filters['label_id']);
            });
        }

        // Поиск по названию
        if (! empty($filters['search'])) {
            $query->where('title', 'like', '%'.$filters['search'].'%');
        }

        return $query->latest()->paginate($perPage);
    }
}
