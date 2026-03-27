<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\IO;

use App\Models\ConversationState;
use Illuminate\Http\Request;

class TelegramRequest
{
    private ConversationState $conversationState;

    private int $telegramUserId;

    public function __construct(
        private readonly Request $request
    ) {
        $strTelegramUserId = $this->request->post()['message']['chat']['id'] ?? '';

        if ($strTelegramUserId === '') {
            throw new \Exception('Invalid telegram request');
        }

        $this->telegramUserId = intval($strTelegramUserId);
    }

    public static function fromHTTPRequest(Request $request): self
    {
        return new self($request);
    }

    public function getMessage(): string
    {
        return $this->request->post()['message']['text'] ?? '';
    }

    public function getTelegramUserId(): int
    {
        return $this->telegramUserId;
    }

    public function setConversationState(ConversationState $conversationState): self
    {
        $this->conversationState = $conversationState;

        return $this;
    }

    public function getConversationState(): ConversationState
    {
        return $this->conversationState;
    }
}
