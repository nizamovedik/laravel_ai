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
            'tasks_count' => $this->tasks_count ?? $this->tasks->count(),
            'created_at' => $this->created_at->toISOString(),
            'tasks' => $this->whenLoaded('tasks', function () {
                return $this->tasks->map(fn ($task) => [
                    'id' => $task->id,
                    'name' => $task->title,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'assignee' => $task->assignee,
                ]);
            }),
        ];
    }
}
