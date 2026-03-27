<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Task;

use App\Enums\Task\TaskStatus;
use Illuminate\Support\Carbon;

class CreateTaskDTO
{
    public function __construct(
        private readonly int $telegramUserId,
        private readonly TaskStatus $status,
        private readonly ?string $title = null,
        private readonly ?string $description = null,
        private readonly ?Carbon $deadlineAt = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'telegram_user_id' => $this->telegramUserId,
            'status' => $this->status->value,
            'title' => $this->title,
            'description' => $this->description,
            'deadline_at' => $this->deadlineAt?->toDateTimeString(),
        ];
    }
}
