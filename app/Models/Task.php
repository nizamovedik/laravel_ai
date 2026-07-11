<?php

namespace App\Models;

use App\Enums\TaskStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'project_id',
        'status_id',
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
        'status_id' => TaskStatusEnum::class,
    ];

    // Связи

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class);
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

    // Хелперы

    public function isNew(): bool
    {
        return $this->status?->code === 'new';
    }

    public function isInProgress(): bool
    {
        return $this->status?->code === 'in_progress';
    }

    public function isDone(): bool
    {
        return $this->status?->code === 'done';
    }

    public function isClosed(): bool
    {
        return $this->status?->code === 'closed';
    }

    public function isOverdue(): bool
    {
        return $this->deadline_at && $this->deadline_at->isPast() && ! $this->isClosed();
    }
}
