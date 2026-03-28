<?php

declare(strict_types=1);

namespace App\Console\Commands\TaskNotification;

use App\Contracts\Repositories\TaskNotificationRepositoryInterface;
use App\Contracts\Repositories\TaskRepositoryInterface;
use App\DataTransferObjects\Task\FilterTasksDTO;
use App\DataTransferObjects\TaskNotification\CreateTaskNotificationDTO;
use App\Enums\Task\TaskStatus;
use App\Enums\TaskNotification\TaskNotificationType;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('app:create-task-notifications {--sleep=2} {--limit=50}')]
#[Description('Create remind task notifications.')]
class CreateTaskNotificationsWorker extends Command
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

            $this->createRemindDeadlineNotifications($limit);
            sleep($sleep);
        }
    }

    private function createRemindDeadlineNotifications(int $limit): void
    {
        $dto = new FilterTasksDTO(
            status: TaskStatus::ACTIVE,
            hasNoRemindDeadlineNotification: true,
            limit: $limit,
        );

        $tasks = app(TaskRepositoryInterface::class)->getAll($dto);

        foreach ($tasks as $task) {
            if (!$task->deadline_at) {
                continue;
            }

            try {
                app(TaskNotificationRepositoryInterface::class)->create(
                    new CreateTaskNotificationDTO(
                        taskId: $task->id,
                        type: TaskNotificationType::REMIND_ABOUT_DEADLINE,
                        notifyAt: $task->deadline_at->copy()->subMinute()->subSeconds(4),
                    )
                );
            } catch (\Throwable $e) {
                Log::channel('tasklog')->error('Error when creating remind deadline task notification', [
                    'task_id' => $task->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        if ($tasks->count() > 0) {
            $this->info('Created notifications for '.$tasks->count().' tasks');
        }
    }
}
