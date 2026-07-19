<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatusEnum;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use InvalidArgumentException;

class TaskController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(
        private TaskService $taskService,
    ) {}

    /**
     * Список задач с фильтрацией
     */
    public function index(): AnonymousResourceCollection
    {
        $filters = request()->only(['status_id', 'assignee_id', 'project_id', 'label_id', 'search']);

        $tasks = $this->taskService->getFilteredTasks($filters, 5);

        return TaskResource::collection($tasks);
    }

    /**
     * Детали одной задачи
     */
    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task);

        return new TaskResource($task->load(['priority', 'creator', 'assignee', 'project']));
    }

    /**
     * Создание задачи
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $taskData = $request->toTaskData();
        $task = $this->taskService->createTask($taskData);

        return response()->json([
            'message' => 'Задача успешно создана',
            'task' => new TaskResource($task->load(['project', 'priority', 'creator', 'assignee'])),
        ], 201);
    }

    public function update(Task $task, UpdateTaskRequest $request): JsonResponse
    {
        $this->authorize('update', $task);

        $userId = auth()->id();

        if (! $userId) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $taskData = $request->toTaskData();
        $updatedTask = $this->taskService->updateTask($task, $taskData, $userId);

        return response()->json([
            'message' => 'Задача успешно обновлена',
            'task' => new TaskResource($updatedTask->load(['priority', 'creator', 'assignee', 'project', 'labels'])),
        ], 200);
    }

    public function updateStatus(Task $task, UpdateTaskStatusRequest $request): JsonResponse
    {
        $this->authorize('updateStatus', $task);

        $newStatus = TaskStatusEnum::tryFrom($request->input('status'));

        if (! $newStatus) {
            return response()->json(['error' => 'Некорректный статус'], 422);
        }

        try {
            $this->taskService->changeStatus(
                task: $task,
                newStatus: $newStatus,
                changedByUserId: auth()->id()
            );

            return response()->json(['message' => 'Статус обновлён'], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Удаление задачи
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $this->taskService->deleteTask($task);

        return response()->json(['message' => 'Задача удалена'], 200);
    }
}
