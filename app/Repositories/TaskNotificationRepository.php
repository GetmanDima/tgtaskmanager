<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\TaskNotificationRepositoryInterface;
use App\DataTransferObjects\TaskNotification\CreateTaskNotificationDTO;
use App\DataTransferObjects\TaskNotification\FilterTaskNotificationsDTO;
use App\DataTransferObjects\TaskNotification\UpdateTaskNotificationDTO;
use App\Models\TaskNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TaskNotificationRepository implements TaskNotificationRepositoryInterface
{
    public function findOrFail(int $id): TaskNotification
    {
        return TaskNotification::query()->findOrFail($id);
    }

    public function create(CreateTaskNotificationDTO $dto): TaskNotification
    {
        return TaskNotification::query()->create($dto->toArray());
    }

    public function update(int $id, UpdateTaskNotificationDTO $dto): TaskNotification
    {
        $model = $this->findOrFail($id);
        $model->fill($dto->toArray());
        $model->save();

        return $model;
    }

    public function getAll(FilterTaskNotificationsDTO $dto): Collection
    {
        return TaskNotification::query()
            ->when($dto->getNotifyAtBeforeOrEqual() !== null, function (Builder $query) use ($dto) {
                return $query->where('notify_at', '<=', $dto->getNotifyAtBeforeOrEqual());
            })
            ->when($dto->getIsNotNotified(), function (Builder $query) {
                return $query->whereNull('notified_at');
            })
            ->when($dto->getNotifyTriesLower() !== null, function (Builder $query) use ($dto) {
                return $query->where('notify_tries', '<', $dto->getNotifyTriesLower());
            })
            ->when($dto->getLimit() !== null, function (Builder $query) use ($dto) {
                /** @var int $limit */
                $limit = $dto->getLimit();

                return $query->limit($limit);
            })
            ->get();
    }

    public function incrementNotifyTries(array $ids): void
    {
        if (count($ids) === 0) {
            return;
        }

        TaskNotification::query()
            ->whereIn('id', $ids)
            ->increment('notify_tries');
    }
}
