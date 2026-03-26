<?php

declare(strict_types=1);

namespace App\Enums\ConversationState;

/**
 * task_message_input
 * {
 *     task_id: 1,
 *     filled_fields: [],
 *     unfilled_fields: [],
 * }
 */
enum ConversationStateValue: string
{
    case START = 'start';
    case TASK_CREATE = 'task_create';
    case TASK_UPDATE = 'task_update';
}
