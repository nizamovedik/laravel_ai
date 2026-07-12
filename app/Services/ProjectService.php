<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectService
{
    public function __construct(
        private ProjectRepository $repository
    ) {}

    public function getUserProjects(int $userId): Collection
    {
        return $this->repository->getUserProjects($userId);
    }

    public function createProject(array $data, int $userId): Project
    {
        return DB::transaction(function () use ($data, $userId) {
            $project = $this->repository->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'owner_id' => $userId,
                'team_lead_id' => $data['team_lead_id'] ?? $userId,
                'status_id' => 1,
                'started_at' => $data['started_at'] ?? null,
                'deadline_at' => $data['deadline_at'] ?? null,
            ]);

            Log::info("Проект {$project->id} создан пользователем {$userId}");

            return $project;
        });
    }

    public function updateProject(Project $project, array $data): Project
    {
        $this->repository->update($project, $data);

        Log::info("Проект {$project->id} обновлён");

        return $project->fresh();
    }

    public function deleteProject(Project $project): void
    {
        $this->repository->delete($project);

        Log::info("Проект {$project->id} удалён");
    }
}
