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
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'project_id' => $this->projectId,
            'creator_id' => $this->creatorId,
            'priority_id' => $this->priorityId,
            'assignee_id' => $this->assigneeId,
            'deadline_at' => $this->deadlineAt,
            'estimated_hours' => $this->estimatedHours,
        ];
    }
}
