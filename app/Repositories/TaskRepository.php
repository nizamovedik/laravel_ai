<?php

namespace App\Repositories;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
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

    public function syncLabels(Task $task, array $labelIds): void
    {
        $task->labels()->sync($labelIds);
    }

    public function attachLabels(Task $task, array $labelIds): void
    {
        $task->labels()->attach($labelIds);
    }

    public function detachLabels(Task $task, array $labelIds): void
    {
        $task->labels()->detach($labelIds);
    }
}
