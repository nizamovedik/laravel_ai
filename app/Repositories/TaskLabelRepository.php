<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\TaskLabel;
use Illuminate\Database\Eloquent\Collection;

class TaskLabelRepository
{
    public function create(array $data): TaskLabel
    {
        return TaskLabel::create($data);
    }

    public function findById(int $id): ?TaskLabel
    {
        return TaskLabel::find($id);
    }

    public function getAll(): Collection
    {
        return TaskLabel::all();
    }

    public function update(TaskLabel $label, array $data): bool
    {
        return $label->update($data);
    }

    public function delete(TaskLabel $label): bool
    {
        return $label->delete($label->id);
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
