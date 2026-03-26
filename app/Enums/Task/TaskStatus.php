<?php

declare(strict_types=1);

namespace App\Enums\Task;

enum TaskStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case DONE = 'done';
    case DELETED = 'deleted';

    public function translate(): string
    {
        return match ($this) {
            self::DRAFT => 'Черновик',
            self::ACTIVE => 'Активная',
            self::DONE => 'Завершена',
            self::DELETED => 'Удалена',
        };
    }
}
