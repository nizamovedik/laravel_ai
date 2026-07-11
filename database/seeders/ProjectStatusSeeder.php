<?php

namespace Database\Seeders;

use App\Models\ProjectStatus;
use Illuminate\Database\Seeder;

class ProjectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Черновик', 'code' => 'draft', 'sort' => 0, 'is_default' => true],
            ['name' => 'Активный', 'code' => 'active', 'sort' => 10],
            ['name' => 'На паузе', 'code' => 'on_hold', 'sort' => 20],
            ['name' => 'Завершён', 'code' => 'completed', 'sort' => 30],
            ['name' => 'Архивирован', 'code' => 'archived', 'sort' => 40],
        ];

        foreach ($statuses as $status) {
            ProjectStatus::create($status);
        }
    }
}
