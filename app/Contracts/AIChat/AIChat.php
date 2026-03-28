<?php

declare(strict_types=1);

namespace App\Contracts\AIChat;

interface AIChat
{
    public function ask(string $message, int $maxTries): AIChatResponse;
}
