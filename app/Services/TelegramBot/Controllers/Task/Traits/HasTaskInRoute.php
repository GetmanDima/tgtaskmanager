<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\Controllers\Task\Traits;

use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Enums\Task\TaskStatus;
use App\Models\Task;
use App\Services\TelegramBot\IO\TelegramResponse;
use Illuminate\Http\Client\RequestException;

trait HasTaskInRoute
{
    /**
     * @throws RequestException
     */
    private function getTaskFromMessage(string $message): ?Task
    {
        $taskId = $this->getTaskId($message);

        if ($taskId === null) {
            app(TelegramResponse::class)->sendMessage(
                'Вы не указали идентификатор задачи',
            );

            return null;
        }

        $task = app(TaskRepositoryInterface::class)->find($taskId);

        if ($task === null || $task->status === TaskStatus::DELETED) {
            app(TelegramResponse::class)->sendMessage(
                'Задача #'.$taskId.' не найдена',
            );

            return null;
        }

        return $task;
    }

    private function getTaskId(string $message): ?int
    {
        $strId = explode(' ', $message)[1] ?? null;

        return $strId === null ? null : intval($strId);
    }
}
