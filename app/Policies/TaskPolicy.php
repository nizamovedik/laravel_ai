<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Все авторизованные пользователи могут видеть список задач
    }

    public function view(User $user, Task $task): bool
    {
        // Пользователь может видеть задачу, если он её создал, назначен исполнителем,
        // или является владельцем/тимлидом проекта
        return $user->id === $task->creator_id
            || $user->id === $task->assignee_id
            || $user->id === $task->project?->owner_id
            || $user->id === $task->project?->team_lead_id;
    }

    public function create(User $user): bool
    {
        return true; // Любой авторизованный пользователь может создавать задачи
    }

    public function update(User $user, Task $task): bool
    {
        // Обновлять задачу может только создатель или исполнитель
        return $user->id === $task->creator_id
            || $user->id === $task->assignee_id
            || $user->id === $task->project?->owner_id
            || $user->id === $task->project?->team_lead_id;
    }

    public function updateStatus(User $user, Task $task): bool
    {
        // Менять статус может только создатель или исполнитель
        return $user->id === $task->creator_id
            || $user->id === $task->assignee_id
            || $user->id === $task->project?->owner_id
            || $user->id === $task->project?->team_lead_id;
    }

    public function delete(User $user, Task $task): bool
    {
        // Удалять задачу может только создатель или владелец проекта
        return $user->id === $task->creator_id
            || $user->id === $task->project?->owner_id
            || $user->id === $task->project?->team_lead_id;
    }
}
