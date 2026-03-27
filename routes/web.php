<?php

declare(strict_types=1);

use App\Http\Controllers\TelegramBotWebhookController;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::post('/telegram-bot/webhook/'.config('telegram_bot.webhook_route_key'), TelegramBotWebhookController::class)
    ->withoutMiddleware('web')
    ->middleware(SubstituteBindings::class)
    ->name('telegram-bot-webhook');
