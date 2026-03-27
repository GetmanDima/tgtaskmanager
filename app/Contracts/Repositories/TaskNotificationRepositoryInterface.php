<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DataTransferObjects\TaskNotification\CreateTaskNotificationDTO;
use App\DataTransferObjects\TaskNotification\FilterTaskNotificationsDTO;
use App\DataTransferObjects\TaskNotification\UpdateTaskNotificationDTO;
use App\Models\TaskNotification;
use Illuminate\Database\Eloquent\Collection;

interface TaskNotificationRepositoryInterface
{
    public function findOrFail(int $id): TaskNotification;

    public function create(CreateTaskNotificationDTO $dto): TaskNotification;

    public function update(int $id, UpdateTaskNotificationDTO $dto): TaskNotification;

    /**
     * @return Collection<int, TaskNotification>
     */
    public function getAll(FilterTaskNotificationsDTO $dto): Collection;

    /**
     * @param  list<int>  $ids
     */
    public function incrementNotifyTries(array $ids): void;
}
