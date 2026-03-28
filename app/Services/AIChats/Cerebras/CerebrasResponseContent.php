<?php

declare(strict_types=1);

namespace App\Services\AIChats\Cerebras;

use App\Contracts\AIChat\AIChatResponseContent;
use App\Enums\AIChat\AIChatResponseContentType;
use Illuminate\Http\Client\Response;

class CerebrasResponseContent implements AIChatResponseContent
{
    public function __construct(
        private readonly Response $response,
    ) {}

    public function getType(): AIChatResponseContentType
    {
        return AIChatResponseContentType::TEXT;
    }

    public function getText(): string
    {
        return $this->response->json()['choices'][0]['message']['content'] ?? '';
    }
}
