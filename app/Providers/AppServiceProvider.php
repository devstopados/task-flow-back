<?php

namespace App\Providers;

use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\SprintRepositoryInterface;
use App\Repositories\Contracts\StatusTaskRepositoryInterface;
use App\Repositories\Contracts\SubprojectRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\SprintRepository;
use App\Repositories\StatusTaskRepository;
use App\Repositories\SubprojectRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(SubprojectRepositoryInterface::class, SubprojectRepository::class);
        $this->app->bind(StatusTaskRepositoryInterface::class, StatusTaskRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(SprintRepositoryInterface::class, SprintRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
