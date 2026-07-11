<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatusEnum;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $taskData = $request->toTaskData();
        $task = $this->taskService->createTask($taskData);

        return response()->json([
            'message' => 'Задача успешно создана',
            'task' => $task->load('project', 'status', 'priority', 'creator', 'assignee'),
        ], 201);
    }

    public function updateStatus(Task $task, UpdateTaskStatusRequest $request): JsonResponse
    {
        $newStatus = TaskStatusEnum::tryFrom($request->input('status_id'));

        if (! $newStatus) {
            return response()->json(['error' => 'Некорректный статус'], 422);
        }

        try {

            $userId = auth()->id();

            if (! $userId) {
                return response()->json(['error' => 'Пользователь не авторизован'], 401);
            }

            $this->taskService->changeStatus(
                task: $task,
                newStatus: $newStatus,
                changedByUserId: $userId
            );

            return response()->json(['message' => 'Статус обновлён'], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
