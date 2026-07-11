<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskLabel;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $projects = Project::all();
        $statuses = TaskStatus::all();
        $priorities = TaskPriority::all();
        $labels = TaskLabel::all();

        // Создаём 10-20 задач
        for ($i = 1; $i <= 15; $i++) {
            $task = Task::create([
                'title' => "Задача №{$i}: ".fake()->sentence(3),
                'description' => fake()->paragraph(),
                'project_id' => $projects->random()->id,
                'status_id' => $statuses->random()->id,
                'priority_id' => $priorities->random()->id,
                'creator_id' => $users->random()->id,
                'assignee_id' => $users->random()->id,
                'deadline_at' => fake()->optional()->dateTimeBetween('now', '+1 month'),
                'estimated_hours' => fake()->optional()->numberBetween(1, 20),
            ]);

            // Прикрепляем 1-3 случайных тега
            $task->labels()->attach($labels->random(rand(1, 3))->pluck('id')->toArray());
        }
    }
}
