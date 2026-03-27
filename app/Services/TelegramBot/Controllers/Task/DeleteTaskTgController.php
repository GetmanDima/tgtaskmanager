<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\Controllers\Task;

use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Services\TelegramBot\Controllers\Task\Traits\HasTaskInRoute;
use App\Services\TelegramBot\Controllers\TelegramController;
use App\Services\TelegramBot\IO\TelegramRequest;
use App\Services\TelegramBot\IO\TelegramResponse;
use Illuminate\Http\Client\RequestException;

class DeleteTaskTgController extends TelegramController
{
    use HasTaskInRoute;

    public function __construct(
        private readonly TelegramResponse $response,
        private readonly TaskRepositoryInterface $taskRepository,
    ) {}

    /**
     * @throws RequestException
     */
    public function handle(TelegramRequest $request): void
    {
        $message = $request->getMessage();
        $task = $this->getTaskFromMessage($message);

        if ($task === null) {
            return;
        }

        $this->taskRepository->delete($task->id);

        $this->response->sendMessage('Задача #'.$task->id.' удалена');
    }
}
