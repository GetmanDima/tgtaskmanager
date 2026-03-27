<?php

declare(strict_types=1);

namespace App\DataTransferObjects\TaskNotification;

use Illuminate\Support\Carbon;

class UpdateTaskNotificationDTO
{
    public function __construct(
        private readonly ?Carbon $notifiedAt = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'notified_at' => $this->notifiedAt?->toDateTimeString(),
        ]);
    }
}
