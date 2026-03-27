<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Task;

use App\Enums\Task\TaskStatus;
use Illuminate\Support\Carbon;

class UpdateTaskDTO
{
    public function __construct(
        private readonly ?TaskStatus $status = null,
        private readonly ?string $title = null,
        private readonly ?string $description = null,
        private readonly ?Carbon $deadlineAt = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'status' => $this->status?->value,
            'title' => $this->title,
            'description' => $this->description,
            'deadline_at' => $this->deadlineAt?->toDateTimeString(),
        ], fn ($value) => $value !== null);
    }
}
