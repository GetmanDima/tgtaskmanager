<?php

declare(strict_types=1);

namespace App\Console\Commands\TaskNotification;

use App\Contracts\Repositories\TaskNotificationRepositoryInterface;
use App\DataTransferObjects\TaskNotification\FilterTaskNotificationsDTO;
use App\Enums\TaskNotification\TaskNotificationType;
use App\Jobs\SendRemindDeadlineTaskNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('app:send-task-notifications {--sleep=2} {--limit=50}')]
#[Description('Send task notifications to telegram.')]
class SendTaskNotificationsWorker extends Command
{
    private const array NOTIFICATION_HANDLERS = [
        TaskNotificationType::REMIND_ABOUT_DEADLINE->value => SendRemindDeadlineTaskNotification::class,
    ];

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

            $this->sendNotifications($limit);
            sleep($sleep);
        }
    }

    private function sendNotifications(int $limit): void
    {
        $notifications = app(TaskNotificationRepositoryInterface::class)->getAll(
            new FilterTaskNotificationsDTO(
                notifyAtBeforeOrEqual: Carbon::now(),
                notifyTriesLower: 2,
                limit: $limit,
            )
        );

        foreach ($notifications as $notification) {
            $notificationHandler = self::NOTIFICATION_HANDLERS[$notification->type->value];
            $notificationHandler::dispatch($notification);
        }

        /** @var list<int> $ids */
        $ids = $notifications->pluck('id')->toArray();
        app(TaskNotificationRepositoryInterface::class)->incrementNotifyTries($ids);

        if ($notifications->count() > 0) {
            $this->info('Processed '.$notifications->count().' notifications');
        }
    }
}
