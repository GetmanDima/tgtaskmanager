<?php

declare(strict_types=1);

return [
    'token' => env('TELEGRAM_BOT_TOKEN', ''),
    'webhook_route_key' => env('TELEGRAM_BOT_WEBHOOK_ROUTE_KEY', 'secret'),

    'commands' => [
        ['command' => 'start', 'description' => 'Вывод списка команд'],
        ['command' => 'new', 'description' => 'Создание новой задачи'],
        ['command' => 'tasks', 'description' => 'Список активных задач пользователя'],
        ['command' => 'done', 'description' => 'Список завершенных задач пользователя'],
        ['command' => 'draft', 'description' => 'Список черновиков пользователя'],
        ['command' => 'task', 'description' => 'Необходимо указать <id>. Просмотр задачи'],
        ['command' => 'edit', 'description' => 'Необходимо указать <id>. Редактирование задачи'],
        ['command' => 'delete', 'description' => 'Необходимо указать <id>. Удаление задачи'],
        ['command' => 'cancel', 'description' => 'Отмена текущего действия'],
    ],
];
