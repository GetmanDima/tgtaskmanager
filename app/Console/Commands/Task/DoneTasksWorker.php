<?php

declare(strict_types=1);

namespace App\Console\Commands\Task;

use App\Contracts\Repositories\TaskRepositoryInterface;
use App\DataTransferObjects\Task\FilterTasksDTO;
use App\Enums\Task\TaskStatus;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('app:done-tasks {--sleep=2} {--limit=50}')]
#[Description('Move expired active tasks to done status.')]
class DoneTasksWorker extends Command
{
    private bool $stopWorker = false;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->trap([SIGTERM, SIGINT], function () {
            $this->stopWorker = true;
        });

        $sleep = intval($this->option('sleep'));
        $limit = intval($this->option('limit'));

        if ($sleep < 1) {
            throw new \Exception('Invalid sleep value');
        }

        if ($limit < 1) {
            throw new \Exception('Invalid limit value');
        }

        for (; ;) {
            if ($this->stopWorker) {
                break;
            }

            $this->moveActiveTasksToDone($limit);
            sleep($sleep);
        }
    }

    private function moveActiveTasksToDone(int $limit): void
    {
        $dto = new FilterTasksDTO(
            status: TaskStatus::ACTIVE,
            deadlineBeforeOrEqual: Carbon::now(),
            limit: $limit,
        );

        $tasks = app(TaskRepositoryInterface::class)->getAll($dto);

        /** @var list<int> $ids */
        $ids = $tasks->pluck('id')->toArray();
        app(TaskRepositoryInterface::class)->doneTasks($ids);

        if ($tasks->count() > 0) {
            $this->info('Processed '.$tasks->count().' tasks');
        }
    }
}
