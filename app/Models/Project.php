<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status_id',
        'owner_id',
        'team_lead_id',
        'started_at',
        'deadline_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'deadline_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | СВЯЗИ
    |--------------------------------------------------------------------------
    */

    /**
     * Пользователь, который создал проект (владелец)
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Руководитель проекта (Team Lead)
     */
    public function teamLead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }

    /**
     * Статус проекта (активный, архивный и т.д.)
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id');
    }

    /**
     * Все задачи, принадлежащие проекту
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Комментарии к проекту (полиморфная связь)
     * (добавим позже, когда создадим таблицу comments)
     */
    // public function comments(): MorphMany
    // {
    //     return $this->morphMany(Comment::class, 'commentable');
    // }

    /*
    |--------------------------------------------------------------------------
    | ХЕЛПЕРЫ (вспомогательные методы)
    |--------------------------------------------------------------------------
    */

    /**
     * Проверяет, активен ли проект
     */
    public function isActive(): bool
    {
        return $this->status?->code === 'active';
    }

    /**
     * Проверяет, архивирован ли проект
     */
    public function isArchived(): bool
    {
        return $this->status?->code === 'archived';
    }

    /**
     * Проверяет, является ли пользователь владельцем проекта
     */
    public function isOwner(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    /**
     * Проверяет, является ли пользователь руководителем проекта
     */
    public function isTeamLead(User $user): bool
    {
        return $this->team_lead_id === $user->id;
    }
}
