<?php

namespace App\Models;

use App\Enums\TaskStatusEnum;
use App\Models\Scopes\TaskScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'project_id',
        'status',
        'priority_id',
        'creator_id',
        'assignee_id',
        'deadline_at',
        'started_at',
        'completed_at',
        'estimated_hours',
    ];

    protected $casts = [
        'deadline_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'status' => TaskStatusEnum::class,
    ];

    // Связи

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TaskPriority::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(TaskLabel::class, 'task_label_task');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // Локальные скоупы
    public function scopeOverdue($query)
    {
        return TaskScopes::overdue($query);
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return TaskScopes::assignedTo($query, $userId);
    }

    public function scopeWithStatus($query, string $status)
    {
        return TaskScopes::withStatus($query, $status);
    }

    public function scopeInProject($query, int $projectId)
    {
        return TaskScopes::inProject($query, $projectId);
    }
}
