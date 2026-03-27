<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\Controllers;

use App\Services\TelegramBot\IO\TelegramRequest;
use App\Services\TelegramBot\IO\TelegramResponse;
use Illuminate\Http\Client\RequestException;

class StartTgController extends TelegramController
{
    public function __construct(
        private readonly TelegramResponse $response,
    ) {}

    /**
     * @throws RequestException
     */
    public function handle(TelegramRequest $request): void
    {
        $this->response->sendMessage(
            "В данном боте вы можете создавать задачи по описанию и получать напоминания.\n".
            "Команды:\n".
            implode(
                ";\n",
                array_map(
                    function ($command) {
                        return '/'.$command['command'].' - '.$command['description'];
                    },
                    config('telegram_bot.commands') ?? [],
                )
            )
        );

    }
}
