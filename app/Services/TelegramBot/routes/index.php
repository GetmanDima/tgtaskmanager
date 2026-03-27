<?php

declare(strict_types=1);

use App\Enums\ConversationState\ConversationStateValue;
use App\Services\TelegramBot\Controllers\StartTgController;

return [
    ['state' => ConversationStateValue::START, 'message' => '/start', 'controller' => StartTgController::class],
];
