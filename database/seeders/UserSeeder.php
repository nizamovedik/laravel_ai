<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаём конкретных пользователей для тестирования
        User::factory()->admin()->create([
            'name' => 'Администратор',
            'email' => 'admin@test.com',
        ]);

        User::factory()->manager()->create([
            'name' => 'Руководитель',
            'email' => 'manager@test.com',
        ]);

        User::factory()->create([
            'name' => 'Исполнитель',
            'email' => 'executor@test.com',
        ]);

        // Генерируем ещё 20 случайных пользователей
        User::factory(20)->create();
    }
}
