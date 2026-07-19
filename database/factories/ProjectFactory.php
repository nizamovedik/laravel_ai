<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->paragraph(),
            'status_id' => 1, // предположим, что 1 = 'активный'
            'owner_id' => User::factory(),
            'team_lead_id' => User::factory(),
            'started_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'deadline_at' => $this->faker->dateTimeBetween('+1 month', '+6 months'),
        ];
    }
}
