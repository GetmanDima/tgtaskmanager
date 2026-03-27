<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\Controllers\Task;

use App\Enums\Task\TaskStatus;
use App\Services\TelegramBot\Controllers\Task\Traits\HasShowTasks;
use App\Services\TelegramBot\Controllers\TelegramController;
use App\Services\TelegramBot\IO\TelegramRequest;
use Illuminate\Http\Client\RequestException;

class ShowDraftTasksTgController extends TelegramController
{
    use HasShowTasks;

    /**
     * @throws RequestException
     */
    public function handle(TelegramRequest $request): void
    {
        $this->showTasks(TaskStatus::DRAFT, 'Черновики');
    }
}
