<?php

namespace App\Http\Requests;

use App\Enums\TaskStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statuses = collect(TaskStatusEnum::cases())->map(fn ($case) => $case->value)->join(',');

        return [
            'status' => "required|string|in:{$statuses}",
        ];
    }
}
