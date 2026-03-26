<?php

declare(strict_types=1);

namespace App\Enums\TaskNotification;

enum TaskNotificationType: string
{
    case REMIND_ABOUT_DEADLINE = 'remind_about_deadline';
}
