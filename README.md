# Telegram Task Manager

## Getting Started: Development Environment

Setting up the development environment is straightforward with Docker.

1.  **Clone the Repository**:
    ```bash
    git clone https://github.com/GetmanDima/tgtaskmanager
    cd tgtaskmanager
    ```

2.  **Configure Environment**:
    ```bash
    cp .env.example .env
    ```
    *You may customize database credentials and other settings inside `.env`.*

3.  **Launch Services**:
    ```bash
    docker compose up -d
    ```

4.  **Application Setup**:
    ```bash
    docker exec -it task_manager_app composer run setup
    ```

5.  **Run queue**:
    ```bash
    docker exec -it task_manager_app php artisan queue:listen redis --sleep=1 --tries=1 --queue=default
    ```
    
6. **Run workers**:
    ```bash
    docker exec -it task_manager_app php artisan app:done-tasks
    docker exec -it task_manager_app php artisan app:create-task-notifications
    docker exec -it task_manager_app php artisan app:send-task-notifications
    ```

## Getting Started: Production Environment
1.  **Clone the Repository**:
    ```bash
    git clone https://github.com/GetmanDima/tgtaskmanager
    cd tgtaskmanager
    ```

2.  **Configure Environment**:
    ```bash
    cp .env.prod.example .env
    ```
    *You may customize database credentials and other settings inside `.env`.*
    You also should fill APP_KEY, DB_USERNAME, DB_PASSWORD, DB_ROOT_PASSWORD, REDIS_PASSWORD, CEREBRAS_API_KEY, TELEGRAM_BOT_TOKEN, TELEGRAM_BOT_WEBHOOK_ROUTE_KEY in .env

3.  **Launch Services**:
    ```bash
    docker compose up -d
    docker exec -it task_manager_app supervisorctl stop workers:*
    docker exec -it task_manager_app supervisorctl stop queue
    docker exec -it task_manager_app composer install --no-dev --no-interaction --no-plugins --no-scripts --prefer-dist --optimize-autoloader
    docker exec -it task_manager_app php artisan migrate --force
    docker exec -it task_manager_app php artisan optimize
    docker exec -it task_manager_app supervisorctl start queue
    docker exec -it task_manager_app supervisorctl start workers:*
    ```

## Other Useful Commands
-   **Configure telegram bot**:
    ```bash
    docker exec -it task_manager_app php artisan app:configure-telegram-bot {url}
    ```
    
-   **Generate Model PHPDocs**:
    ```bash
    docker exec -it task_manager_app composer run ide-helper
    ```

-   **Run phpstan**:
    ```bash
    docker exec -it task_manager_app composer run phpstan
    ```

-   **Run pint**:
    ```bash
    docker exec -it task_manager_app composer run pint
    ```

## AI
Used AI Service: Cerebras.
Model available for free: ``qwen-3-235b-a22b-instruct-2507``
Parameters in .env.
```
CEREBRAS_API_KEY=
CEREBRAS_MODEL="qwen-3-235b-a22b-instruct-2507"
CEREBRAS_MAX_TRIES=3
```

## Telegram Bot
Parameters in .env
```
TELEGRAM_BOT_TOKEN=
TELEGRAM_BOT_WEBHOOK_ROUTE_KEY=secret
```
