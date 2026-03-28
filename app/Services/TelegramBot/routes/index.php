<?php

declare(strict_types=1);

use App\Enums\ConversationState\ConversationStateValue;
use App\Services\TelegramBot\Controllers\CancelTgController;
use App\Services\TelegramBot\Controllers\StartTgController;
use App\Services\TelegramBot\Controllers\Task\Create\CreateTaskTgController;
use App\Services\TelegramBot\Controllers\Task\Create\NewTaskTgController;
use App\Services\TelegramBot\Controllers\Task\DeleteTaskTgController;
use App\Services\TelegramBot\Controllers\Task\ShowActiveTasksTgController;
use App\Services\TelegramBot\Controllers\Task\ShowDoneTasksTgController;
use App\Services\TelegramBot\Controllers\Task\ShowDraftTasksTgController;
use App\Services\TelegramBot\Controllers\Task\ShowSingleTaskTgController;
use App\Services\TelegramBot\Controllers\Task\Update\EditTaskTgController;
use App\Services\TelegramBot\Controllers\Task\Update\UpdateTaskTgController;

return [
    ['message' => '/cancel', 'controller' => CancelTgController::class],
    ['state' => ConversationStateValue::START, 'message' => '/start', 'controller' => StartTgController::class],

    ['state' => ConversationStateValue::START, 'message' => '/tasks', 'controller' => ShowActiveTasksTgController::class],
    ['state' => ConversationStateValue::START, 'message' => '/done', 'controller' => ShowDoneTasksTgController::class],
    ['state' => ConversationStateValue::START, 'message' => '/draft', 'controller' => ShowDraftTasksTgController::class],
    ['state' => ConversationStateValue::START, 'pattern' => '/^\/task (\d+)$/', 'controller' => ShowSingleTaskTgController::class],

    ['state' => ConversationStateValue::START, 'message' => '/new', 'controller' => NewTaskTgController::class],
    ['state' => ConversationStateValue::TASK_CREATE, 'controller' => CreateTaskTgController::class],

    ['state' => ConversationStateValue::START, 'pattern' => '/^\/edit (\d+)$/', 'controller' => EditTaskTgController::class],
    ['state' => ConversationStateValue::TASK_UPDATE, 'controller' => UpdateTaskTgController::class],

    ['state' => ConversationStateValue::START, 'pattern' => '/^\/delete (\d+)$/', 'controller' => DeleteTaskTgController::class],
];
