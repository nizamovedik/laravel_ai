<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $activeStatusId = ProjectStatus::where('code', 'active')->first()->id;
        $archivedStatusId = ProjectStatus::where('code', 'archived')->first()->id;
        $draftStatusId = ProjectStatus::where('code', 'draft')->first()->id;

        Project::create([
            'name' => 'Пет-проект: Доска задач',
            'description' => 'Разработка системы управления задачами',
            'status_id' => $activeStatusId,
            'owner_id' => $users->first()->id,
            'team_lead_id' => $users->skip(1)->first()->id,
            'started_at' => now(),
            'deadline_at' => now()->addMonth(),
        ]);

        Project::create([
            'name' => 'Закрытый проект 2024',
            'description' => 'Законченный проект для тестов',
            'status_id' => $archivedStatusId,
            'owner_id' => $users->first()->id,
            'started_at' => now()->subYear(),
            'deadline_at' => now()->subMonths(6),
        ]);

        Project::create([
            'name' => 'Идея для стартапа',
            'status_id' => $draftStatusId,
            'owner_id' => $users->skip(2)->first()->id,
        ]);
    }
}
