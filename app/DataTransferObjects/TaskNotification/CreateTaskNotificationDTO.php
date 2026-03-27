<?php

declare(strict_types=1);

namespace App\DataTransferObjects\TaskNotification;

use App\Enums\TaskNotification\TaskNotificationType;
use Illuminate\Support\Carbon;

class CreateTaskNotificationDTO
{
    public function __construct(
        private readonly int $taskId,
        private readonly TaskNotificationType $type,
        private readonly Carbon $notifyAt,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'task_id' => $this->taskId,
            'type' => $this->type->value,
            'notify_at' => $this->notifyAt->toDateTimeString(),
        ];
    }
}
