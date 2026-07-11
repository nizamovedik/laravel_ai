<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Support\Collection;

class TaskRepository
{
    public function create(array $data): Task
    {
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

    public function updateStatus(Task $task, int $statusId): bool
    {
        return $task->update(['status_id' => $statusId]);
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
}
