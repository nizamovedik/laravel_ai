<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskLabelRequest;
use App\Http\Requests\UpdateTaskLabelRequest;
use App\Http\Resources\TaskLabelResource;
use App\Models\TaskLabel;
use App\Services\TaskLabelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskLabelController extends Controller
{
    public function __construct(
        private TaskLabelService $service
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return TaskLabelResource::collection($this->service->getAllLabels());
    }

    public function store(StoreTaskLabelRequest $request): TaskLabelResource
    {
        $label = $this->service->createLabel($request->toTaskLabelData());

        return new TaskLabelResource($label);
    }

    public function update(UpdateTaskLabelRequest $request, TaskLabel $taskLabel): TaskLabelResource
    {
        $label = $this->service->updateLabel($taskLabel, $request->toTaskLabelData());

        return new TaskLabelResource($label);
    }

    public function destroy(TaskLabel $taskLabel): JsonResponse
    {
        $this->service->deleteLabel($taskLabel);

        return response()->json(['message' => 'Тег удалён'], 200);
    }
}
