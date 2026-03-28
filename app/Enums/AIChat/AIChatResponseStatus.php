<?php

declare(strict_types=1);

namespace App\Enums\AIChat;

enum AIChatResponseStatus: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
}
