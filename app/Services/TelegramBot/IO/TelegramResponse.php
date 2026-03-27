<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\IO;

use Illuminate\Http\Client\RequestException;

class TelegramResponse
{
    public function __construct(
        private readonly TelegramBotAPI $telegramBotAPI,
        private readonly TelegramRequest $telegramRequest,
    ) {}

    /**
     * @throws RequestException
     */
    public function sendMessage(string $message): void
    {
        $telegramUserId = $this->telegramRequest->getTelegramUserId();
        $this->telegramBotAPI->sendMessage($telegramUserId, $message);
    }

    /**
     * @throws RequestException
     */
    public function sendUnhandledErrorMessage(): void
    {
        $telegramUserId = $this->telegramRequest->getTelegramUserId();
        $this->telegramBotAPI->sendMessage($telegramUserId, 'Произошла неизвестная ошибка');
    }
}
