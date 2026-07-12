<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Авторизация через Policy уже проверяется в контроллере
        // Поэтому здесь просто возвращаем true
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'team_lead_id' => 'nullable|exists:users,id',
            'started_at' => 'nullable|date',
            'deadline_at' => 'nullable|date|after:started_at',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Название проекта обязательно',
            'deadline_at.after' => 'Дата окончания должна быть позже даты начала',
        ];
    }
}
