<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskPriorityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'level' => $this->level,
            'color' => $this->color ?? '#6b7280',
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
