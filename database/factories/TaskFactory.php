<?php

namespace Database\Factories;

use App\Enums\TaskStatusEnum;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'project_id' => Project::factory(),
            'status' => TaskStatusEnum::NEW->value,
            'priority_id' => null,
            'creator_id' => User::factory(),
            'assignee_id' => null,
            'deadline_at' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'started_at' => null,
            'completed_at' => null,
            'estimated_hours' => $this->faker->numberBetween(1, 40),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
