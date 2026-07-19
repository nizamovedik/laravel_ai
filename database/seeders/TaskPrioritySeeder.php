<?php

namespace Database\Seeders;

use App\Models\TaskPriority;
use Illuminate\Database\Seeder;

class TaskPrioritySeeder extends Seeder
{
    public function run(): void
    {
        $priorities = [
            ['name' => 'Низкий', 'code' => 'low', 'level' => 1, 'color' => '#10b981'],
            ['name' => 'Средний', 'code' => 'medium', 'level' => 2, 'color' => '#f59e0b'],
            ['name' => 'Высокий', 'code' => 'high', 'level' => 3, 'color' => '#f97316'],
            ['name' => 'Критический', 'code' => 'critical', 'level' => 4, 'color' => '#ef4444'],
        ];

        foreach ($priorities as $priority) {
            TaskPriority::create($priority);
        }
    }
}
