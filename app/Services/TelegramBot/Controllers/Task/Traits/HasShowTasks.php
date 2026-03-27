<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\Controllers\Task\Traits;

use App\Contracts\Repositories\TaskRepositoryInterface;
use App\DataTransferObjects\Task\FilterTasksDTO;
use App\Enums\Task\TaskStatus;
use App\Services\TelegramBot\IO\TelegramRequest;
use App\Services\TelegramBot\IO\TelegramResponse;
use Illuminate\Http\Client\RequestException;

trait HasShowTasks
{
    /**
     * @throws RequestException
     */
    private function showTasks(TaskStatus $status, string $messagePrefix, int $limit = 50): void
    {
        $dto = new FilterTasksDTO(
            telegramUserId: app(TelegramRequest::class)->getTelegramUserId(),
            status: $status,
            limit: $limit,
            orderByDirection: 'desc',
        );

        $tasks = app(TaskRepositoryInterface::class)->getAll($dto);

        $message = $tasks->map(function ($task) {
            return '#'.$task->id.' '.($task->title ?? '-').' '.$task->deadline_at?->format('d.m.Y H:i');
        })->join("\n");

        if ($tasks->count() > 0) {
            $message = $messagePrefix.
                ($tasks->count() >= $limit ? " (последние $limit)" : '').
                ":\n".$message;
        } else {
            $message = $messagePrefix.' не найдены.';
        }

        app(TelegramResponse::class)->sendMessage($message);
    }
}
