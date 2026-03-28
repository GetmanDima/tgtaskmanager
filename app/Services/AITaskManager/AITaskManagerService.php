<?php

declare(strict_types=1);

namespace App\Services\AITaskManager;

use App\Contracts\AIChat\AIChat;
use App\Contracts\AIChat\AIChatResponse;
use App\Contracts\AIChat\AIChatResponseContent;
use App\Contracts\Repositories\TaskRepositoryInterface;
use App\DataTransferObjects\Task\CreateTaskDTO;
use App\DataTransferObjects\Task\TaskFromMessageResponseDTO;
use App\DataTransferObjects\Task\UpdateTaskDTO;
use App\Enums\AIChat\AIChatResponseStatus;
use App\Enums\Task\TaskStatus;
use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AITaskManagerService
{
    private const array REQUIRED_TASK_FIELDS = [
        'title',
        'description',
        'deadline_at',
    ];

    public function __construct(
        private readonly AIChat $chat,
        private readonly TaskRepositoryInterface $taskRepository,
    ) {}

    /**
     * @throws \Exception
     */
    public function createTaskFromMessage(int $telegramUserId, string $message): TaskFromMessageResponseDTO
    {
        $chatResponse = $this->askChat($this->getFullCreateTaskMessage($message));
        $content = $chatResponse->getContent();

        try {
            $fieldsWithValues = $this->getValidatedChatResponseData($content);
        } catch (\Exception $e) {
            Log::channel('tgbotlog')->error('Invalid task AI response data', [
                'text' => $content->getText(),
                'message' => $message,
            ]);

            $fieldsWithValues = [];
        }

        $unfilledFields = $this->getUnfilledFields($fieldsWithValues);

        $dto = new CreateTaskDTO(
            telegramUserId: $telegramUserId,
            status: count($unfilledFields) > 0 ? TaskStatus::DRAFT : TaskStatus::ACTIVE,
            title: $fieldsWithValues['title'] ?? null,
            description: $fieldsWithValues['description'] ?? null,
            deadlineAt: isset($fieldsWithValues['deadline_at']) ? new Carbon($fieldsWithValues['deadline_at']) : null,
        );
        $task = $this->taskRepository->create($dto);

        return new TaskFromMessageResponseDTO(
            task: $task,
            unfilledFields: $unfilledFields,
            aiMessage: $fieldsWithValues['ai_message'] ?? '',
        );
    }

    /**
     * @throws \Exception
     */
    public function updateTaskFromMessage(Task $task, string $message): TaskFromMessageResponseDTO
    {
        $chatResponse = $this->askChat($this->getFullUpdateTaskMessage($task, $message));
        $content = $chatResponse->getContent();

        try {
            $fieldsWithValues = $this->getValidatedChatResponseData($content);
        } catch (\Exception $e) {
            Log::channel('tgbotlog')->error('Invalid task AI response data', [
                'text' => $content->getText(),
                'message' => $message,
            ]);

            $fieldsWithValues = [];
        }

        $unfilledFields = $this->getUnfilledFields($fieldsWithValues, $task);

        $dto = new UpdateTaskDTO(
            status: count($unfilledFields) > 0 ? TaskStatus::DRAFT : TaskStatus::ACTIVE,
            title: $fieldsWithValues['title'],
            description: $fieldsWithValues['description'],
            deadlineAt: isset($fieldsWithValues['deadline_at']) ? new Carbon($fieldsWithValues['deadline_at']) : null,
        );
        $task = $this->taskRepository->update($task->id, $dto);

        return new TaskFromMessageResponseDTO(
            task: $task,
            unfilledFields: $unfilledFields,
            aiMessage: $fieldsWithValues['ai_message'] ?? '',
        );
    }

    private function getFullCreateTaskMessage(string $userMessage): string
    {
        $stubPath = resource_path('stubs/ai_chat/task_create_message.txt');

        if (!File::exists($stubPath)) {
            throw new \Exception('Create task message does not exist');
        }

        $content = File::get($stubPath);

        return strtr(
            $content,
            [
                '{current_time}' => now()->toDateTimeString(),
                '{user_message}' => $userMessage,
            ],
        );
    }

    private function getFullUpdateTaskMessage(Task $task, string $userMessage): string
    {
        $stubPath = resource_path('stubs/ai_chat/task_update_message.txt');

        if (!File::exists($stubPath)) {
            throw new \Exception('Update task message does not exist');
        }

        $content = File::get($stubPath);

        return strtr(
            $content,
            [
                '{task_title}' => $task->title ?? 'null',
                '{task_description}' => $task->description ?? 'null',
                '{task_deadline}' => $task->deadline_at?->toDatetimeString() ?? 'null',
                '{current_time}' => now()->toDateTimeString(),
                '{user_message}' => $userMessage,
            ]
        );
    }

    private function askChat(string $message): AIChatResponse
    {
        $response = $this->chat->ask($message, intval(config('ai_chat.cerebras.max_tries')));

        if ($response->getStatus() === AIChatResponseStatus::ERROR) {
            throw new \Exception('AI chat message did not sent');
        }

        return $response;
    }

    /**
     * @return array<string, ?string>
     *
     * @throws \Exception
     */
    private function getValidatedChatResponseData(AIChatResponseContent $content): array
    {
        $fieldsWithValues = json_decode($content->getText(), true);

        if (!is_array($fieldsWithValues)) {
            throw new \Exception('Invalid task AI response data');
        }

        $validator = Validator::make(
            $fieldsWithValues,
            [
                'title' => ['nullable', 'string'],
                'description' => ['nullable', 'string'],
                'deadline_at' => ['nullable', 'date_format:Y-m-d H:i:s', 'after:now'],
            ]
        );

        if ($validator->fails()) {
            $invalidFields = $validator->errors()->keys();

            foreach ($invalidFields as $field) {
                $fieldsWithValues[$field] = null;
            }
        }

        return $fieldsWithValues;
    }

    /**
     * @param  array<string, ?string>  $fieldsWithValues
     * @return list<string>
     */
    private function getUnfilledFields(array $fieldsWithValues, ?Task $task = null): array
    {
        $unfilledFields = [];

        foreach (self::REQUIRED_TASK_FIELDS as $field) {
            if (
                !isset($fieldsWithValues[$field]) &&
                $task?->{$field} === null
            ) {
                $unfilledFields[] = $field;
            }
        }

        return $unfilledFields;
    }
}
