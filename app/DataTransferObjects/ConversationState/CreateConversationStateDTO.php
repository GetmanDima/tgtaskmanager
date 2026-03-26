<?php

declare(strict_types=1);

namespace App\DataTransferObjects\ConversationState;

use App\Enums\ConversationState\ConversationStateValue;

class CreateConversationStateDTO
{
    /**
     * @param  array<mixed>|null  $payload
     */
    public function __construct(
        private readonly int $telegramUserId,
        private readonly ConversationStateValue $state,
        private readonly ?array $payload = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'telegram_user_id' => $this->telegramUserId,
            'state' => $this->state->value,
            'payload' => $this->payload,
        ];
    }
}
