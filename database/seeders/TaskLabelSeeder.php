<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskLabel;
use Illuminate\Database\Seeder;

class TaskLabelSeeder extends Seeder
{
    public function run(): void
    {
        // Создаём теги
        $labels = TaskLabel::all();

        if ($labels->isEmpty()) {
            $labels = TaskLabel::factory(8)->create();
        }

        $tasks = Task::all();

        foreach ($tasks as $task) {
            // ✅ Выбираем уникальные теги (чтобы не было дублей)
            $randomLabels = $labels->random(rand(1, 3))->pluck('id')->unique()->toArray();
            $task->labels()->sync($randomLabels); // sync — заменяет все теги
        }
    }
}
