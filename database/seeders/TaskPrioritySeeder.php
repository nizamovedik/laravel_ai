<?php

namespace Database\Seeders;

use App\Models\TaskPriority;
use Illuminate\Database\Seeder;

class TaskPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            ['name' => 'Низкий', 'code' => 'low', 'level' => 0],
            ['name' => 'Средний', 'code' => 'medium', 'level' => 1],
            ['name' => 'Высокий', 'code' => 'high', 'level' => 2],
            ['name' => 'Критичный', 'code' => 'critical', 'level' => 3],
        ];

        foreach ($priorities as $priority) {
            TaskPriority::create($priority);
        }
    }
}
