<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\Repositories\TaskNotificationRepositoryInterface;
use App\DataTransferObjects\TaskNotification\UpdateTaskNotificationDTO;
use App\Enums\TaskNotification\TaskNotificationType;
use App\Models\TaskNotification;
use App\Services\TelegramBot\IO\TelegramBotAPI;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SendRemindDeadlineTaskNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly TaskNotification $notification,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $notification = $this->notification;

            if ($notification->notified_at) {
                return;
            }

            // @phpstan-ignore notIdentical.alwaysFalse
            if ($notification->type !== TaskNotificationType::REMIND_ABOUT_DEADLINE) {
                throw new \Exception('Expected remind about deadline notification type in notification #'.$notification->id);
            }

            $task = $notification->task;
            $message = 'Напоминание о скором завершении задачи #'.$task->id;

            app(TelegramBotAPI::class)->sendMessage(
                $task->telegram_user_id,
                $message,
            );

            $dto = new UpdateTaskNotificationDTO(
                notifiedAt: Carbon::now()
            );
            app(TaskNotificationRepositoryInterface::class)->update($notification->id, $dto);
        } catch (\Throwable $e) {
            Log::channel('tasklog')->error('Error when sending task notification', [
                'notification_id' => $notification->id,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
