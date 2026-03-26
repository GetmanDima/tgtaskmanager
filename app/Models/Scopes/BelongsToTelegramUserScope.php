<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Services\TelegramBot\IO\TelegramRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BelongsToTelegramUserScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (!app()->bound(TelegramRequest::class)) {
            return;
        }

        $telegramRequest = app(TelegramRequest::class);

        $builder->where('telegram_user_id', '=', $telegramRequest->getTelegramUserId());
    }
}
