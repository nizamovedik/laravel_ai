<?php

namespace App\Http\Requests;

use App\DTO\TaskLabelData;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskLabelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:task_labels,name',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string',
        ];
    }

    public function toTaskLabelData(): TaskLabelData
    {
        return new TaskLabelData(
            name: $this->input('name'),
            color: $this->input('color'),
            description: $this->input('description'),
        );
    }
}
