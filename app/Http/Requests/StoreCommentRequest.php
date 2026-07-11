<?php

namespace App\Http\Requests;

use App\DTO\CommentData;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => 'required|string|min:1|max:2000',
        ];
    }

    public function toCommentData(string $type, int $id): CommentData
    {
        return new CommentData(
            userId: (int) $this->user()->id,
            body: $this->input('body'),
            commentableType: $type,
            commentableId: $id,
        );
    }
}
