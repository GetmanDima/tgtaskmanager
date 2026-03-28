<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\Controllers\Task\Update;

use App\Contracts\Repositories\ConversationStateRepositoryInterface;
use App\Contracts\Repositories\TaskRepositoryInterface;
use App\DataTransferObjects\ConversationState\CreateConversationStateDTO;
use App\Enums\ConversationState\ConversationStateValue;
use App\Services\AITaskManager\AITaskManagerService;
use App\Services\TelegramBot\Controllers\Task\Traits\HasShowSingleTask;
use App\Services\TelegramBot\Controllers\TelegramController;
use App\Services\TelegramBot\IO\TelegramRequest;
use App\Services\TelegramBot\IO\TelegramResponse;
use Illuminate\Http\Client\RequestException;

class UpdateTaskTgController extends TelegramController
{
    use HasShowSingleTask;

    public function __construct(
        private readonly TelegramResponse $response,
        private readonly AITaskManagerService $taskManagerService,
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly ConversationStateRepositoryInterface $conversationStateRepository,
    ) {}

    /**
     * @throws RequestException
     */
    public function handle(TelegramRequest $request): void
    {
        $message = $request->getMessage();
        $taskId = $this->getTaskId($request);

        if ($taskId === null) {
            $this->response->sendMessage(
                "Некорректное состояние запроса. Попробуйте отредактировать еще раз \n"
            );

            return;
        }

        $task = $this->taskRepository->find($taskId);

        if ($task === null) {
            $this->response->sendMessage(
                'Задаче #'.$taskId.' не найдена',
            );

            return;
        }

        $response = $this->taskManagerService->updateTaskFromMessage($task, $message);
        $task = $response->getTask();

        if ($response->getUnfilledFields()) {
            $prefixText = "Задача обновлена.\n".
                "Комментарий от модели: \n".
                $response->getAiMessage()."\n".
                'Следующим сообщением дополните пожалуйста недостающие данные.';
        } else {
            $prefixText = "Обновление задачи завершено.\n".
                "Удалось извлечь все данные.\n".
                "Комментарий от модели: \n".
                (empty($response->getAiMessage()) ? 'У модели нет уточняющих вопросов' : $response->getAiMessage());
        }

        $this->showSingleTask($task, $prefixText);

        if (!$response->getUnfilledFields()) {
            $dto = new CreateConversationStateDTO(
                $request->getTelegramUserId(),
                ConversationStateValue::START,
            );
            $this->conversationStateRepository->create($dto);
        }
    }

    private function getTaskId(TelegramRequest $request): ?int
    {
        return $request->getConversationState()->payload['task_id'] ?? null;
    }
}
