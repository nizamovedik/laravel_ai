<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Проверка через Policy в контроллере
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'team_lead_id' => 'nullable|exists:users,id',
            'started_at' => 'nullable|date',
            'deadline_at' => 'nullable|date|after:started_at',
        ];
    }
}
