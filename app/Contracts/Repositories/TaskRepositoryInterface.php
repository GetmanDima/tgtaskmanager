<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DataTransferObjects\Task\CreateTaskDTO;
use App\DataTransferObjects\Task\FilterTasksDTO;
use App\DataTransferObjects\Task\UpdateTaskDTO;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function find(int $id): ?Task;

    public function findOrFail(int $id): Task;

    public function create(CreateTaskDTO $dto): Task;

    public function update(int $id, UpdateTaskDTO $dto): Task;

    public function delete(int $id): Task;

    /**
     * @return Collection<int, Task>
     */
    public function getAll(FilterTasksDTO $dto): Collection;

    /**
     * @param  list<int>  $ids
     */
    public function doneTasks(array $ids): void;
}
