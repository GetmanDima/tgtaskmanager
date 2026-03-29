<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Task\TaskStatus;
use App\Models\Scopes\BelongsToTelegramUserScope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $telegram_user_id
 * @property TaskStatus $status
 * @property string|null $title
 * @property string|null $description
 * @property Carbon|null $deadline_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, TaskNotification> $notifications
 * @property-read int|null $notifications_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDeadlineAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTelegramUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'telegram_user_id',
        'status',
        'title',
        'description',
        'deadline_at',
    ];

    protected $casts = [
        'deadline_at' => 'datetime',
        'status' => TaskStatus::class,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BelongsToTelegramUserScope);

        static::updated(function (Task $task) {
            $task->notifications()
                ->whereNull('notified_at')
                ->delete();
        });
    }

    /**
     * @return HasMany<TaskNotification, $this>
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(TaskNotification::class);
    }
}
