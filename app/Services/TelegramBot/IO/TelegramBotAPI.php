<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\IO;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TelegramBotAPI
{
    /**
     * @throws RequestException
     */
    public function sendMessage(int $telegramUserId, string $message): void
    {
        Http::post($this->getBotUrl().'/sendMessage', [
            'chat_id' => $telegramUserId,
            'text' => Str::limit($message, 4000),
        ])->throw();
    }

    /**
     * @throws RequestException
     * @throws \Exception
     */
    public function setWebhook(string $url): void
    {
        Http::post($this->getBotUrl().'/setWebhook', [
            'url' => $url,
        ])->throw();
    }

    /**
     * @throws RequestException
     */
    public function deleteWebhook(): void
    {
        Http::get($this->getBotUrl().'/deleteWebhook')->throw();
    }

    /**
     * @param  list<array{command: string, description: string}>  $commands
     *
     * @throws RequestException
     * @throws \Exception
     */
    public function setCommands(array $commands): void
    {
        Http::post($this->getBotUrl().'/setMyCommands', [
            'commands' => $commands,
        ])->throw();
    }

    private function getBotUrl(): string
    {
        if (!config('telegram_bot.token')) {
            throw new \Exception('Telegram bot token not found');
        }

        return 'https://api.telegram.org/bot'.config('telegram_bot.token');
    }
}
