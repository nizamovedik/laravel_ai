<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

class UserScopes
{
    /**
     * Фильтр: только активные пользователи
     */
    public static function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Фильтр: пользователи с определённой ролью
     */
    public static function withRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }
}
