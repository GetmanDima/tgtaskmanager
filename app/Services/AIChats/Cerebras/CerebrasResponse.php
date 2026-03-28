<?php

declare(strict_types=1);

namespace App\Services\AIChats\Cerebras;

use App\Contracts\AIChat\AIChatResponse;
use App\Contracts\AIChat\AIChatResponseContent;
use App\Enums\AIChat\AIChatResponseStatus;
use Illuminate\Http\Client\Response;

class CerebrasResponse implements AIChatResponse
{
    public function __construct(
        private readonly Response $response,
    ) {}

    public function getHttpResponse(): Response
    {
        return $this->response;
    }

    public function getStatus(): AIChatResponseStatus
    {
        return $this->response->successful() ? AIChatResponseStatus::SUCCESS : AIChatResponseStatus::ERROR;
    }

    public function getContent(): AIChatResponseContent
    {
        return new CerebrasResponseContent($this->response);
    }
}
