<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\TaskRepositoryInterface;
use App\DataTransferObjects\Task\CreateTaskDTO;
use App\DataTransferObjects\Task\FilterTasksDTO;
use App\DataTransferObjects\Task\UpdateTaskDTO;
use App\Enums\Task\TaskStatus;
use App\Enums\TaskNotification\TaskNotificationType;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function find(int $id): ?Task
    {
        return Task::query()->find($id);
    }

    public function findOrFail(int $id): Task
    {
        return Task::query()->findOrFail($id);
    }

    public function create(CreateTaskDTO $dto): Task
    {
        return Task::query()->create($dto->toArray());
    }

    public function update(int $id, UpdateTaskDTO $dto): Task
    {
        $model = $this->findOrFail($id);
        $model->fill($dto->toArray());
        $model->save();

        return $model;
    }

    public function delete(int $id): Task
    {
        $model = $this->findOrFail($id);
        $model->status = TaskStatus::DELETED;
        $model->save();

        return $model;
    }

    public function getAll(FilterTasksDTO $dto): Collection
    {
        return Task::query()
            ->when($dto->getTelegramUserId() !== null, function (Builder $query) use ($dto) {
                $query->where('telegram_user_id', '=', $dto->getTelegramUserId());
            })
            ->when($dto->getStatus() !== null, function (Builder $query) use ($dto) {
                $query->where('status', '=', $dto->getStatus()?->value);
            })
            ->when($dto->getDeadLineAfterOrEqual() !== null, function (Builder $query) use ($dto) {
                $query->where('deadline_at', '>=', $dto->getDeadLineAfterOrEqual()?->toDateTimeString());
            })
            ->when($dto->getDeadLineBeforeOrEqual() !== null, function (Builder $query) use ($dto) {
                $query->where('deadline_at', '<=', $dto->getDeadLineBeforeOrEqual()?->toDateTimeString());
            })
            ->when($dto->getLimit() !== null, function (Builder $query) use ($dto) {
                /** @var int $limit */
                $limit = $dto->getLimit();
                $query->limit($limit);
            })
            ->when($dto->hasNoRemindDeadlineNotification(), function (Builder $query) {
                $query->whereDoesntHave('notifications', function ($query) {
                    $query->where('type', '=', TaskNotificationType::REMIND_ABOUT_DEADLINE->value);
                });
            })
            ->orderBy($dto->getOrderByColumn(), $dto->getOrderByDirection())
            ->get();
    }

    public function doneTasks(array $ids): void
    {
        if (count($ids) === 0) {
            return;
        }

        Task::query()->whereIn('id', $ids)->update(['status' => TaskStatus::DONE]);
    }
}
