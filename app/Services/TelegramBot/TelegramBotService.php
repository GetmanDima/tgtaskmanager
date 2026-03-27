<?php

declare(strict_types=1);

namespace App\Services\TelegramBot;

use App\Contracts\Repositories\ConversationStateRepositoryInterface;
use App\DataTransferObjects\ConversationState\CreateConversationStateDTO;
use App\Enums\ConversationState\ConversationStateValue;
use App\Models\ConversationState;
use App\Services\TelegramBot\Controllers\TelegramController;
use App\Services\TelegramBot\IO\TelegramRequest;
use App\Services\TelegramBot\IO\TelegramResponse;
use Illuminate\Http\Request;

class TelegramBotService
{
    /**
     * @var list<array{
     *      state: ?ConversationStateValue,
     *      message: ?string,
     *      pattern: ?string,
     *      controller: class-string<TelegramController>
     *  }>
     */
    private array $routes;

    public function __construct(
        private readonly ConversationStateRepositoryInterface $conversationStateRepository,
    ) {
        $this->routes = require __DIR__.'/routes/index.php';
    }

    public function handleRequest(Request $request): void
    {
        try {
            $telegramRequest = TelegramRequest::fromHTTPRequest($request);
            $telegramUserId = $telegramRequest->getTelegramUserId();

            $conversationState = $this->getConversationState($telegramUserId);
            $telegramRequest->setConversationState($conversationState);

            app()->instance(TelegramRequest::class, $telegramRequest);

            $controller = $this->getController($telegramRequest);

            if ($controller === null) {
                app(TelegramResponse::class)->sendMessage(
                    'Данную команду распознать не удается. Полный список возможных команд можете посмотреть по команде /start'
                );

                return;
            }

            $controller->handle($telegramRequest);
        } catch (\Throwable $exception) {
            if (app()->bound(TelegramRequest::class)) {
                app(TelegramResponse::class)->sendUnhandledErrorMessage();
            }

            throw $exception;
        }
    }

    private function getConversationState(int $telegramUserId): ConversationState
    {
        $conversationState = $this->conversationStateRepository->findLastByTelegramUserId($telegramUserId);

        if ($conversationState === null) {
            $conversationState = $this->conversationStateRepository->create(
                new CreateConversationStateDTO(
                    $telegramUserId,
                    ConversationStateValue::START,
                )
            );
        }

        return $conversationState;
    }

    private function getController(TelegramRequest $request): ?TelegramController
    {
        $suitableController = array_find($this->routes, function ($route) use ($request) {
            return
                (!isset($route['state']) || $request->getConversationState()->state === $route['state']) &&
                (!isset($route['message']) || $request->getMessage() === $route['message']) &&
                (!isset($route['pattern']) || preg_match($route['pattern'], $request->getMessage())) &&
                $route['controller']::customRouteCheck($request);
        })['controller'] ?? null;

        return $suitableController === null ? null : app($suitableController);
    }
}
