<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class InitProjectCounters extends Command
{
    protected $signature = 'project:init-counters';

    protected $description = 'Initialize project task counters in Redis';

    public function handle()
    {
        $projects = Project::withCount('tasks')->get();

        foreach ($projects as $project) {
            Redis::set("project:{$project->id}:tasks_count", $project->tasks_count);
            $this->info("Project {$project->id}: {$project->tasks_count} tasks");
        }

        $this->info('✅ All counters initialized!');
    }
}
