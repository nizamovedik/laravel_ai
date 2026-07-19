<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

class TaskScopes
{
    /**
     * Фильтр: только просроченные задачи (дедлайн меньше текущего времени)
     */
    public static function overdue(Builder $query): Builder
    {
        return $query->where('deadline_at', '<', now())
            ->whereNotIn('status', ['done', 'closed']);
    }

    /**
     * Фильтр: задачи, назначенные на конкретного пользователя
     */
    public static function assignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assignee_id', $userId);
    }

    /**
     * Фильтр: задачи по статусу
     */
    public static function withStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Фильтр: задачи по проекту
     */
    public static function inProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }
}
