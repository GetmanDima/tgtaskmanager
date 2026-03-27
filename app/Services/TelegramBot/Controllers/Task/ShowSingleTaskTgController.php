<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\Controllers\Task;

use App\Services\TelegramBot\Controllers\Task\Traits\HasShowSingleTask;
use App\Services\TelegramBot\Controllers\Task\Traits\HasTaskInRoute;
use App\Services\TelegramBot\Controllers\TelegramController;
use App\Services\TelegramBot\IO\TelegramRequest;
use Illuminate\Http\Client\RequestException;

class ShowSingleTaskTgController extends TelegramController
{
    use HasShowSingleTask, HasTaskInRoute;

    /**
     * @throws RequestException
     */
    public function handle(TelegramRequest $request): void
    {
        $message = $request->getMessage();
        $task = $this->getTaskFromMessage($message);

        if (!$task) {
            return;
        }

        $this->showSingleTask($task);
    }
}
