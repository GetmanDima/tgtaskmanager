<?php

declare(strict_types=1);

namespace App\DataTransferObjects\TaskNotification;

use Illuminate\Support\Carbon;

class FilterTaskNotificationsDTO
{
    public function __construct(
        private readonly ?Carbon $notifyAtBeforeOrEqual = null,
        private readonly bool $isNotNotified = false,
        private readonly ?int $notifyTriesLower = null,
        private readonly ?int $limit = null,
    ) {}

    public function getNotifyAtBeforeOrEqual(): ?Carbon
    {
        return $this->notifyAtBeforeOrEqual;
    }

    public function getIsNotNotified(): bool
    {
        return $this->isNotNotified;
    }

    public function getNotifyTriesLower(): ?int
    {
        return $this->notifyTriesLower;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }
}
