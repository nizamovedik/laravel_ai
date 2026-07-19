<?php

namespace App\DTO;

use Carbon\Carbon;

class TaskData
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly int $projectId,
        public readonly int $creatorId,
        public readonly string $status,
        public readonly ?int $priorityId,
        public readonly ?int $assigneeId,
        public readonly ?Carbon $deadlineAt,
        public readonly ?float $estimatedHours,
        public readonly ?array $labelIds = [],
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'project_id' => $this->projectId,
            'creator_id' => $this->creatorId,
            'status' => $this->status,
            'priority_id' => $this->priorityId,
            'assignee_id' => $this->assigneeId,
            'deadline_at' => $this->deadlineAt,
            'estimated_hours' => $this->estimatedHours,
        ], fn ($value) => ! is_null($value));
    }
}
