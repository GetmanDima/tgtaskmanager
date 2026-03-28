<?php

declare(strict_types=1);

namespace App\Services\AIChats\Cerebras;

use App\Contracts\AIChat\AIChat;
use App\Contracts\AIChat\AIChatResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CerebrasChatService implements AIChat
{
    /**
     * @throws \Exception
     */
    public function ask(string $message, int $maxTries): AIChatResponse
    {
        $response = null;

        for ($i = 1; $i <= max($maxTries, 1); $i++) {
            try {
                $response = $this->sendAPIRequest($message);

                if ($response->successful()) {
                    break;
                }
            } catch (\Throwable $e) {
                Log::channel('cerebraslog')->error('Cerebras api error', [
                    'message' => $e->getMessage(),
                    'user_request_message' => $message,
                    'response_status' => $response?->status(),
                    'response_body' => Str::limit($response?->body() ?? '', 100),
                ]);

                sleep(1);
            }
        }

        if ($response === null) {
            throw new \Exception('Cerebras response not found');
        }

        return new CerebrasResponse($response);
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    private function sendAPIRequest(string $message): Response
    {
        return Http::withHeader(
            'Authorization', 'Bearer '.config('ai_chat.cerebras.api_key')
        )->post('https://api.cerebras.ai/v1/chat/completions', [
            'model' => config('ai_chat.cerebras.model'),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $message,
                ],
            ],
            'stream' => false,
            'temperature' => 0,
            'max_tokens' => -1,
            'seed' => 0,
            'top_p' => 1,
        ])->throw();
    }
}
