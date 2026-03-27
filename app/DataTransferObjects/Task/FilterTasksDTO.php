<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Task;

use App\Enums\Task\TaskStatus;
use Illuminate\Support\Carbon;

class FilterTasksDTO
{
    public function __construct(
        private readonly ?int $telegramUserId = null,
        private readonly ?TaskStatus $status = null,
        private readonly ?Carbon $deadlineAfterOrEqual = null,
        private readonly ?Carbon $deadlineBeforeOrEqual = null,
        private readonly bool $hasNoRemindDeadlineNotification = false,
        private readonly ?int $limit = null,
        private readonly string $orderByDirection = 'asc',
        private readonly string $orderByColumn = 'id',
    ) {}

    public function getTelegramUserId(): ?int
    {
        return $this->telegramUserId;
    }

    public function getStatus(): ?TaskStatus
    {
        return $this->status;
    }

    public function getDeadLineAfterOrEqual(): ?Carbon
    {
        return $this->deadlineAfterOrEqual;
    }

    public function getDeadLineBeforeOrEqual(): ?Carbon
    {
        return $this->deadlineBeforeOrEqual;
    }

    public function hasNoRemindDeadlineNotification(): bool
    {
        return $this->hasNoRemindDeadlineNotification;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOrderByDirection(): string
    {
        return $this->orderByDirection;
    }

    public function getOrderByColumn(): string
    {
        return $this->orderByColumn;
    }
}
