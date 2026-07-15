<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

class ProjectScopes
{
    /**
     * Фильтр: проекты, где пользователь является владельцем или тимлидом
     */
    public static function accessibleByUser(Builder $query, int $userId): Builder
    {
        return $query->where('owner_id', $userId)
            ->orWhere('team_lead_id', $userId);
    }

    /**
     * Фильтр: только активные проекты
     */
    public static function active(Builder $query): Builder
    {
        return $query->where('status_id', 1);
    }
}
