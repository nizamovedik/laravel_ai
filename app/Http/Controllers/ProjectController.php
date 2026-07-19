<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Jobs\GenerateProjectReport;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class ProjectController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(
        private ProjectService $projectService
    ) {}

    /**
     * Список проектов (только те, где пользователь владелец или тимлид)
     */
    public function index(): AnonymousResourceCollection
    {
        $projects = $this->projectService->getUserProjects(auth()->id());

        return ProjectResource::collection($projects);
    }

    /**
     * Детали одного проекта
     */
    public function show(Project $project): ProjectResource
    {
        $this->authorize('view', $project);

        $tasks = Cache::remember("project_tasks_{$project->id}", 3600, function () use ($project) {
            return $project->tasks()
                ->with(['priority', 'assignee', 'creator', 'labels'])
                ->get();
        });

        $project->setRelation('tasks', $tasks);

        return new ProjectResource(
            $project->load(['owner', 'teamLead'])
        );
    }

    /**
     * Создание проекта
     */
    public function store(StoreProjectRequest $request): ProjectResource
    {
        $this->authorize('create', Project::class);

        $project = $this->projectService->createProject(
            $request->validated(),
            auth()->id()
        );

        return new ProjectResource($project->load(['owner', 'teamLead']));
    }

    /**
     * Обновление проекта
     */
    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $this->authorize('update', $project);

        $updatedProject = $this->projectService->updateProject(
            $project,
            $request->validated()
        );

        return new ProjectResource($updatedProject->load(['owner', 'teamLead']));
    }

    /**
     * Удаление проекта
     */
    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $this->projectService->deleteProject($project);

        return response()->json(['message' => 'Проект удалён'], 200);
    }

    public function generateReport(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        GenerateProjectReport::dispatch($project, auth()->id());

        return response()->json([
            'message' => 'Генерация отчёта начата. Проверьте логи для получения ссылки.',
        ], 202);
    }
}
