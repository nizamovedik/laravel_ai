<?php

namespace App\Models;

use App\Models\Scopes\ProjectScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function scopeAccessibleByUser($query, int $userId)
    {
        return ProjectScopes::accessibleByUser($query, $userId);
    }

    public function scopeActive($query)
    {
        return ProjectScopes::active($query);
    }
}
