<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\AIChat\AIChat;
use App\Contracts\AIChat\AIChatResponse;
use App\Contracts\AIChat\AIChatResponseContent;
use App\Contracts\Repositories\ConversationStateRepositoryInterface;
use App\Contracts\Repositories\TaskNotificationRepositoryInterface;
use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Repositories\ConversationStateRepository;
use App\Repositories\TaskNotificationRepository;
use App\Repositories\TaskRepository;
use App\Services\AIChats\Cerebras\CerebrasChatService;
use App\Services\AIChats\Cerebras\CerebrasResponse;
use App\Services\AIChats\Cerebras\CerebrasResponseContent;
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

        $this->app->bind(AIChat::class, CerebrasChatService::class);
        $this->app->bind(AIChatResponse::class, CerebrasResponse::class);
        $this->app->bind(AIChatResponseContent::class, CerebrasResponseContent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
