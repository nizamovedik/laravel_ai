<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Observers\ProjectObserver;
use App\Observers\TaskObserver;
use App\Policies\CommentPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'task' => Task::class,
            'project' => Project::class,
            'comment' => Comment::class,
            'user' => User::class,
        ]);

        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);

        Project::observe(ProjectObserver::class);
        Task::observe(TaskObserver::class);
    }
}
