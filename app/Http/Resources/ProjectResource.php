<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'owner' => $this->owner ? [
                'id' => $this->owner->id,
                'name' => $this->owner->name,
            ] : null,
            'team_lead' => $this->teamLead ? [
                'id' => $this->teamLead->id,
                'name' => $this->teamLead->name,
            ] : null,
            'status_id' => $this->status_id,
            'started_at' => $this->started_at?->toISOString(),
            'deadline_at' => $this->deadline_at?->toISOString(),
            'tasks_count' => $this->tasks_count ?? $this->tasks->count(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'tasks' => $this->whenLoaded('tasks', function () {
                return $this->tasks->map(fn ($task) => [
                    'id' => $task->id,
                    'name' => $task->title,
                    'status' => $task->status,
                    'priority' => $task->priority ? [
                        'id' => $task->priority->id,
                        'name' => $task->priority->name,
                        'code' => $task->priority->code,
                        'level' => $task->priority->level,
                        'color' => $task->priority->color ?? '#6b7280',
                    ] : null,
                    'assignee' => $task->assignee ? [
                        'id' => $task->assignee->id,
                        'name' => $task->assignee->name,
                    ] : null,
                    'deadline_at' => $task->deadline_at?->toISOString(),
                    'created_at' => $task->created_at?->toISOString(),
                ]);
            }),
        ];
    }
}
