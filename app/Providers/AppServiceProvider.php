<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\ConversationStateRepositoryInterface;
use App\Contracts\Repositories\TaskNotificationRepositoryInterface;
use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Repositories\ConversationStateRepository;
use App\Repositories\TaskNotificationRepository;
use App\Repositories\TaskRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(TaskNotificationRepositoryInterface::class, TaskNotificationRepository::class);
        $this->app->bind(ConversationStateRepositoryInterface::class, ConversationStateRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
