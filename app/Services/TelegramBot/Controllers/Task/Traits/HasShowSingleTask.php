<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\Controllers\Task\Traits;

use App\Models\Task;
use App\Services\TelegramBot\IO\TelegramResponse;
use Illuminate\Http\Client\RequestException;

trait HasShowSingleTask
{
    /**
     * @throws RequestException
     */
    private function showSingleTask(Task $task, string $prefixText = ''): void
    {
        app(TelegramResponse::class)->sendMessage(
            $prefixText."\n".
            'Задача #'.$task->id."\n".
            'Статус: '.$task->status->translate()."\n".
            'Дедлайн: '.$task->deadline_at?->format('d.m.Y H:i')."\n".
            'Название: '.$task->title."\n".
            'Описание: '.$task->description
        );
    }
}
