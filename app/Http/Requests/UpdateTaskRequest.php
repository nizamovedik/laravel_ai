<?php

namespace App\Http\Requests;

use App\DTO\TaskData;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Проверка в контроллере через Policy
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'priority_id' => 'nullable|exists:task_priorities,id',
            'assignee_id' => 'nullable|exists:users,id',
            'deadline_at' => 'nullable|date|after:now',
            'estimated_hours' => 'nullable|numeric|min:0|max:999.99',
            'label_ids' => 'nullable|array',
            'label_ids.*' => 'exists:task_labels,id',
        ];
    }

    public function toTaskData(): TaskData
    {
        return new TaskData(
            title: $this->input('title'),
            description: $this->input('description'),
            projectId: 0, // Не обновляется через этот метод
            creatorId: 0,  // Не обновляется через этот метод
            status: $this->input('status') ?: 'new',
            priorityId: $this->has('priority_id') ? (int) $this->input('priority_id') : null,
            assigneeId: $this->has('assignee_id') ? (int) $this->input('assignee_id') : null,
            deadlineAt: $this->has('deadline_at') ? Carbon::parse($this->input('deadline_at')) : null,
            estimatedHours: $this->has('estimated_hours') ? (float) $this->input('estimated_hours') : null,
            labelIds: $this->has('label_ids') ? $this->input('label_ids') : [],
        );
    }
}
