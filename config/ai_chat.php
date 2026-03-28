<?php

declare(strict_types=1);

return [
    'cerebras' => [
        'api_key' => env('CEREBRAS_API_KEY', ''),
        'model' => env('CEREBRAS_MODEL', ''),
        'max_tries' => env('CEREBRAS_MAX_TRIES', 1),
    ],
];
