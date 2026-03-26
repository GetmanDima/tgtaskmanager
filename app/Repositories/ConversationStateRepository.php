<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\ConversationStateRepositoryInterface;
use App\DataTransferObjects\ConversationState\CreateConversationStateDTO;
use App\Models\ConversationState;

class ConversationStateRepository implements ConversationStateRepositoryInterface
{
    public function create(CreateConversationStateDTO $dto): ConversationState
    {
        return ConversationState::query()->create($dto->toArray());
    }

    public function findLastByTelegramUserId(int $telegramUserId): ?ConversationState
    {
        return ConversationState::query()
            ->where('telegram_user_id', $telegramUserId)
            ->orderByDesc('id')
            ->first();
    }
}
