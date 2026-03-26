<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ConversationState\ConversationStateValue;
use App\Models\Scopes\BelongsToTelegramUserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $telegram_user_id
 * @property ConversationStateValue $state
 * @property array<array-key, mixed>|null $payload
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationState newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationState newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationState query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationState whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationState whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationState whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationState wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationState whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationState whereTelegramUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationState whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ConversationState extends Model
{
    protected $fillable = [
        'telegram_user_id',
        'state',
        'payload',
    ];

    protected $casts = [
        'state' => ConversationStateValue::class,
        'payload' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BelongsToTelegramUserScope);
    }
}
