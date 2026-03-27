<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\Controllers\Task\Update;

use App\Contracts\Repositories\ConversationStateRepositoryInterface;
use App\DataTransferObjects\ConversationState\CreateConversationStateDTO;
use App\Enums\ConversationState\ConversationStateValue;
use App\Enums\Task\TaskStatus;
use App\Services\TelegramBot\Controllers\Task\Traits\HasShowSingleTask;
use App\Services\TelegramBot\Controllers\Task\Traits\HasTaskInRoute;
use App\Services\TelegramBot\Controllers\TelegramController;
use App\Services\TelegramBot\IO\TelegramRequest;
use App\Services\TelegramBot\IO\TelegramResponse;
use Illuminate\Http\Client\RequestException;

class EditTaskTgController extends TelegramController
{
    use HasShowSingleTask, HasTaskInRoute;

    public function __construct(
        private readonly ConversationStateRepositoryInterface $conversationStateRepository,
        private readonly TelegramResponse $telegramResponse,
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

        if (!in_array($task->status, [TaskStatus::ACTIVE, TaskStatus::DRAFT])) {
            $this->telegramResponse->sendMessage(
                'Нельзя изменять задачи со статусом "'.$task->status->translate().'"'
            );

            return;
        }

        $this->showSingleTask($task, 'Опишите, что хотите изменить в задаче.');

        $dto = new CreateConversationStateDTO(
            $request->getTelegramUserId(),
            ConversationStateValue::TASK_UPDATE,
            ['task_id' => $task->id]
        );
        $this->conversationStateRepository->create($dto);
    }
}
