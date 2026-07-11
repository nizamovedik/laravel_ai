<?php

namespace App\Services;

use App\DTO\TaskLabelData;
use App\Models\Task;
use App\Models\TaskLabel;
use App\Repositories\TaskLabelRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskLabelService
{
    public function __construct(
        private TaskLabelRepository $repository
    ) {}

    public function createLabel(TaskLabelData $data): TaskLabel
    {
        return DB::transaction(function () use ($data) {
            $label = $this->repository->create([
                'name' => $data->name,
                'color' => $data->color,
                'description' => $data->description,
            ]);

            Log::info("Тег '{$label->name}' создан");

            return $label;
        });
    }

    public function updateLabel(TaskLabel $label, TaskLabelData $data): TaskLabel
    {
        $this->repository->update($label, [
            'name' => $data->name,
            'color' => $data->color,
            'description' => $data->description,
        ]);

        return $label->fresh();
    }

    public function deleteLabel(TaskLabel $label): void
    {
        $this->repository->delete($label);
    }

    public function getAllLabels(): Collection
    {
        return $this->repository->getAll();
    }

    public function syncTaskLabels(Task $task, array $labelIds): void
    {
        $this->repository->syncLabels($task, $labelIds);
    }
}
