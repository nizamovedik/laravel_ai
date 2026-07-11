<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем первого пользователя (или создаём, если нет)
        $user = User::first() ?? User::factory()->create();

        // Получаем все задачи
        $tasks = Task::all();

        if ($tasks->isEmpty()) {
            // Если задач нет — создаём тестовую
            $task = Task::factory()->create([
                'creator_id' => $user->id,
                'assignee_id' => $user->id,
            ]);
            $tasks = collect([$task]);
        }

        // Для каждой задачи создаём 2–5 комментариев
        foreach ($tasks as $task) {
            Comment::factory()
                ->count(rand(2, 5))
                ->create([
                    'user_id' => $user->id,
                    'commentable_id' => $task->id,
                    'commentable_type' => Task::class,
                ]);
        }

        // Комментарии к проектам

        $projects = Project::all();

        foreach ($projects as $project) {
            Comment::factory()
                ->count(rand(1, 3))
                ->create([
                    'user_id' => $user->id,
                    'commentable_id' => $project->id,
                    'commentable_type' => Project::class,
                ]);
        }
    }
}
