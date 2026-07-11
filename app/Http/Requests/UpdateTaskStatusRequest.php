<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Позже добавим проверку через Policy
    }

    public function rules(): array
    {
        return [
            'status_id' => 'required|integer|exists:task_statuses,id',
        ];
    }
}
