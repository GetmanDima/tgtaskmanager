<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DataTransferObjects\ConversationState\CreateConversationStateDTO;
use App\Models\ConversationState;

interface ConversationStateRepositoryInterface
{
    public function create(CreateConversationStateDTO $dto): ConversationState;

    public function findLastByTelegramUserId(int $telegramUserId): ?ConversationState;
}
