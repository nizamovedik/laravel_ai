<?php

namespace Database\Seeders;

use App\Enums\TaskStatusEnum;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskLabel;
use App\Models\TaskPriority;
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
        $priorities = TaskPriority::all();
        $labels = TaskLabel::all();

        // Создаём 10-20 задач
        for ($i = 1; $i <= 15; $i++) {
            $status = $this->getRandomStatus();

            $task = Task::create([
                'title' => "Задача №{$i}: ".fake()->sentence(3),
                'description' => fake()->paragraph(),
                'project_id' => $projects->random()->id,
                'status' => $status->value,
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

    private function getRandomStatus(): TaskStatusEnum
    {
        // Делаем так, чтобы чаще попадались активные статусы
        $weighted = [
            TaskStatusEnum::NEW,
            TaskStatusEnum::IN_PROGRESS,
            TaskStatusEnum::REVIEW,
            TaskStatusEnum::DONE,
            TaskStatusEnum::CLOSED,
            TaskStatusEnum::ON_HOLD,
        ];

        return $weighted[array_rand($weighted)];
    }
}
