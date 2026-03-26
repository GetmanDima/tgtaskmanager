<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskNotification\TaskNotificationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $task_id
 * @property TaskNotificationType $type
 * @property Carbon $notify_at
 * @property Carbon|null $notified_at
 * @property int $notify_tries
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Task $task
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskNotification whereNotifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskNotification whereNotifyAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskNotification whereNotifyTries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskNotification whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskNotification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskNotification whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TaskNotification extends Model
{
    protected $fillable = [
        'task_id',
        'type',
        'notify_at',
        'notified_at',
        'notify_tries',
    ];

    protected $casts = [
        'type' => TaskNotificationType::class,
        'notify_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Task, $this>
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
