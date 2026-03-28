<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Task;

use App\Models\Task;

class TaskFromMessageResponseDTO
{
    /**
     * @param  list<string>  $unfilledFields
     */
    public function __construct(
        private readonly Task $task,
        private readonly array $unfilledFields,
        private readonly string $aiMessage,
    ) {}

    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @return list<string>
     */
    public function getUnfilledFields(): array
    {
        return $this->unfilledFields;
    }

    public function getAiMessage(): string
    {
        return $this->aiMessage;
    }
}
