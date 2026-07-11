<?php

namespace Database\Seeders;

use App\Models\TaskLabel;
use Illuminate\Database\Seeder;

class TaskLabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $labels = ['Бэкенд', 'Фронтенд', 'База данных', 'DevOps', 'Дизайн', 'Документация'];

        foreach ($labels as $name) {
            TaskLabel::create(['name' => $name]);
        }
    }
}
