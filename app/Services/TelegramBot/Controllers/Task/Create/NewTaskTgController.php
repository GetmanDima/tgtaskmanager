<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\Controllers\Task\Create;

use App\Contracts\Repositories\ConversationStateRepositoryInterface;
use App\DataTransferObjects\ConversationState\CreateConversationStateDTO;
use App\Enums\ConversationState\ConversationStateValue;
use App\Services\TelegramBot\Controllers\TelegramController;
use App\Services\TelegramBot\IO\TelegramRequest;
use App\Services\TelegramBot\IO\TelegramResponse;
use Illuminate\Http\Client\RequestException;

class NewTaskTgController extends TelegramController
{
    public function __construct(
        private readonly TelegramResponse $response,
        private readonly ConversationStateRepositoryInterface $conversationStateRepository,
    ) {}

    /**
     * @throws RequestException
     */
    public function handle(TelegramRequest $request): void
    {
        $this->response->sendMessage(
            'Опишите задачу'
        );

        $dto = new CreateConversationStateDTO(
            $request->getTelegramUserId(),
            ConversationStateValue::TASK_CREATE,
        );

        $this->conversationStateRepository->create($dto);
    }
}
