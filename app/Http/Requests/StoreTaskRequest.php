<?php

namespace App\Http\Requests;

use App\DTO\TaskData;
use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Проверка права на создание задачи в проекте
        return $this->user()->can('create', [Task::class, $this->input('project_id')]);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'priority_id' => 'nullable|exists:task_priorities,id',
            'assignee_id' => 'nullable|exists:users,id',
            'deadline_at' => 'nullable|date|after:now',
            'estimated_hours' => 'nullable|numeric|min:0|max:999.99',
        ];
    }

    public function toTaskData(): TaskData
    {

        return new TaskData(
            title: $this->input('title'),
            description: $this->input('description'),
            projectId: (int) $this->input('project_id'),
            creatorId: (int) $this->user()->id,
            status: TaskStatusEnum::NEW->value,
            priorityId: $this->has('priority_id') ? (int) $this->input('priority_id') : null,
            assigneeId: $this->has('assignee_id') ? (int) $this->input('assignee_id') : null,
            deadlineAt: $this->has('deadline_at') ? Carbon::parse($this->input('deadline_at')) : null,
            estimatedHours: $this->has('estimated_hours') ? (float) $this->input('estimated_hours') : null,
            labelIds: $this->has('label_ids') ? $this->input('label_ids') : [],
        );
    }
}
