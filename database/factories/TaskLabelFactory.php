<?php

namespace Database\Factories;

use App\Models\TaskLabel;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskLabelFactory extends Factory
{
    protected $model = TaskLabel::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'color' => $this->faker->hexColor(),
            'description' => $this->faker->sentence(),
        ];
    }
}
