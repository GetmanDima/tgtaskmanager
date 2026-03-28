<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\Controllers\Task\Create;

use App\Contracts\Repositories\ConversationStateRepositoryInterface;
use App\DataTransferObjects\ConversationState\CreateConversationStateDTO;
use App\Enums\ConversationState\ConversationStateValue;
use App\Services\AITaskManager\AITaskManagerService;
use App\Services\TelegramBot\Controllers\Task\Traits\HasShowSingleTask;
use App\Services\TelegramBot\Controllers\TelegramController;
use App\Services\TelegramBot\IO\TelegramRequest;
use Illuminate\Http\Client\RequestException;

class CreateTaskTgController extends TelegramController
{
    use HasShowSingleTask;

    public function __construct(
        private readonly AITaskManagerService $taskManagerService,
        private readonly ConversationStateRepositoryInterface $conversationStateRepository,
    ) {}

    /**
     * @throws RequestException
     * @throws \Exception
     */
    public function handle(TelegramRequest $request): void
    {
        $message = $request->getMessage();

        $response = $this->taskManagerService->createTaskFromMessage($request->getTelegramUserId(), $message);
        $task = $response->getTask();

        if ($response->getUnfilledFields()) {
            $prefixText =
                "Задача создана. \n".
                "Комментарий от модели: \n".
                $response->getAiMessage()."\n".
                'Следующим сообщением дополните пожалуйста недостающие данные.';
        } else {
            $prefixText = "Создание задачи завершено.\n".
                "Удалось извлечь все данные.\n".
                "Комментарий от модели: \n".
                (empty($response->getAiMessage()) ? 'У модели нет уточняющих вопросов' : $response->getAiMessage());
        }

        $this->showSingleTask($task, $prefixText);

        if ($response->getUnfilledFields()) {
            $dto = new CreateConversationStateDTO(
                $request->getTelegramUserId(),
                ConversationStateValue::TASK_UPDATE,
                ['task_id' => $task->id]
            );
            $this->conversationStateRepository->create($dto);
        } else {
            $dto = new CreateConversationStateDTO(
                $request->getTelegramUserId(),
                ConversationStateValue::START,
            );
            $this->conversationStateRepository->create($dto);
        }
    }
}
