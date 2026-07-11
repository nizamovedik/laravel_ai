<?php

namespace Database\Seeders;

use App\Models\TaskStatus;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Новая', 'code' => 'new', 'sort' => 0, 'is_default' => true],
            ['name' => 'В работе', 'code' => 'in_progress', 'sort' => 10, 'is_default' => false],
            ['name' => 'На ревью', 'code' => 'review', 'sort' => 20, 'is_default' => false],
            ['name' => 'Готово', 'code' => 'done', 'sort' => 30, 'is_default' => false],
            ['name' => 'Закрыта', 'code' => 'closed', 'sort' => 40, 'is_default' => false],
            ['name' => 'Отложена', 'code' => 'on_hold', 'sort' => 50, 'is_default' => false],
        ];

        foreach ($statuses as $status) {
            TaskStatus::create($status);
        }
    }
}
