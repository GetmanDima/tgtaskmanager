<?php

declare(strict_types=1);

namespace App\Services\TelegramBot\Controllers;

use App\Services\TelegramBot\IO\TelegramRequest;

abstract class TelegramController
{
    public static function customRouteCheck(TelegramRequest $request): bool
    {
        return true;
    }

    abstract public function handle(TelegramRequest $request): void;
}
