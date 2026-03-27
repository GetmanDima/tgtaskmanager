<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\TelegramBot\IO\TelegramBotAPI;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;

#[Signature('app:configure-telegram-bot {url}')]
#[Description('Set telegram bot webhook url, set commands to bot')]
class ConfigureTelegramBot extends Command
{
    /**
     * Execute the console command.
     *
     * @throws RequestException
     */
    public function handle(): void
    {
        $url = $this->argument('url');

        if (!$url) {
            throw new \Exception('Please, set url parameter');
        }

        app(TelegramBotAPI::class)->deleteWebhook();
        app(TelegramBotAPI::class)->setWebhook($url);

        if (config('telegram_bot.commands')) {
            app(TelegramBotAPI::class)->setCommands(config('telegram_bot.commands'));
        }
    }
}
