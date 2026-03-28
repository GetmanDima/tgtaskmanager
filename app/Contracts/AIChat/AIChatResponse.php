<?php

declare(strict_types=1);

namespace App\Contracts\AIChat;

use App\Enums\AIChat\AIChatResponseStatus;
use Illuminate\Http\Client\Response;

interface AIChatResponse
{
    public function getHttpResponse(): Response;

    public function getStatus(): AIChatResponseStatus;

    public function getContent(): AIChatResponseContent;
}
