<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatusEnum;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskLabelService;
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
        private TaskLabelService $taskLabelService
    ) {}

    /**
     * Список задач с фильтрацией
     */
    public function index(): AnonymousResourceCollection
    {
        $filters = request()->only(['status_id', 'assignee_id', 'project_id', 'label_id', 'search']);

        $tasks = $this->taskService->getFilteredTasks($filters, 5);
        // $tasks = Task::query()
        //     ->with(['status', 'priority', 'creator', 'assignee', 'project'])
        //     ->when(request('status_id'), fn ($q, $v) => $q->where('status_id', $v))
        //     ->when(request('assignee_id'), fn ($q, $v) => $q->where('assignee_id', $v))
        //     ->when(request('project_id'), fn ($q, $v) => $q->where('project_id', $v))
        //     ->when(request('label_id'), function ($q, $v) {
        //         return $q->whereHas('labels', function ($query) use ($v) {
        //             $query->where('task_labels.id', $v);
        //         });
        //     })
        //     ->when(request('search'), fn ($q, $v) => $q->where('title', 'like', "%{$v}%"))
        //     ->latest()
        //     ->paginate(20);

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

    /**
     * Обновление задачи (поля, кроме статуса)
     */
    public function update(Task $task, UpdateTaskRequest $request): JsonResponse
    {
        $this->authorize('update', $task);

        $userId = auth()->id();

        if (! $userId) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        $taskData = $request->toTaskData();
        $updatedTask = $this->taskService->updateTask($task, $taskData, $userId);

        if ($request->has('label_ids')) {
            $this->taskLabelService->syncTaskLabels($task, $request->input('label_ids'));
        }

        return response()->json([
            'message' => 'Задача успешно обновлена',
            'task' => new TaskResource($updatedTask->load(['priority', 'creator', 'assignee', 'project', 'labels'])),
        ], 200);
    }

    /**
     * Смена статуса задачи
     */
    public function updateStatus(Task $task, UpdateTaskStatusRequest $request): JsonResponse
    {
        $this->authorize('updateStatus', $task);

        $newStatus = TaskStatusEnum::tryFrom($request->input('status'));

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
