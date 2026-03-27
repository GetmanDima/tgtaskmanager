<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\TelegramBot\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TelegramBotWebhookController extends Controller
{
    public function __construct(
        private readonly TelegramBotService $telegramBotService,
    ) {}

    public function __invoke(Request $request): Response
    {
        try {
            $this->telegramBotService->handleRequest($request);
        } catch (\Throwable $e) {
            Log::channel('tgbotlog')->error($e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
        }

        return response()->noContent();
    }
}
