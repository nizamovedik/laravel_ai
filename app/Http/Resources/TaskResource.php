<?php

namespace App\Http\Resources;

use App\Enums\TaskStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->when($this->status, function () {
                $status = $this->status instanceof TaskStatusEnum
                    ? $this->status
                    : TaskStatusEnum::tryFrom($this->status);

                return $status ? [
                    'value' => $status->value,
                    'name' => $status->label(),
                    'color' => $status->color(),
                ] : null;
            }),
            'priority' => $this->when($this->priority, function () {
                return [
                    'id' => $this->priority->id,
                    'name' => $this->priority->name,
                    'code' => $this->priority->code,
                    'level' => $this->priority->level,
                    'color' => $this->priority->color ?? '#6b7280',
                ];
            }),
            'creator' => $this->creator ? [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ] : null,
            'assignee' => $this->assignee ? [
                'id' => $this->assignee->id,
                'name' => $this->assignee->name,
            ] : null,
            'project' => $this->project ? [
                'id' => $this->project->id,
                'name' => $this->project->name,
            ] : null,
            'labels' => $this->whenLoaded('labels', function () {
                return $this->labels->map(fn ($label) => [
                    'id' => $label->id,
                    'name' => $label->name,
                    'color' => $label->color,
                ]);
            }),
            'deadline_at' => $this->deadline_at?->toISOString(),
            'started_at' => $this->started_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'estimated_hours' => $this->estimated_hours,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
