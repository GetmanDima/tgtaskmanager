<?php

declare(strict_types=1);

namespace App\Contracts\AIChat;

use App\Enums\AIChat\AIChatResponseContentType;

interface AIChatResponseContent
{
    public function getType(): AIChatResponseContentType;

    public function getText(): string;
}
