<?php

namespace App\Services;

use App\Models\ChatbotMessage;
use App\Models\User;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class ChatbotService
{
    public function __construct(
        private readonly HttpFactory $http,
    ) {
    }

    public function respond(User $user, array $payload): array
    {
        $message = $this->normalizeMessage((string) ($payload['message'] ?? ''));
        $history = $this->resolveHistory($user, $payload['history'] ?? []);
        $context = is_array($payload['context'] ?? null) ? $payload['context'] : [];

        $requestId = (string) str()->uuid();
        $startedAt = microtime(true);
        $provider = $this->resolveProvider();

        try {
            $response = $this->http
                ->withOptions([
                    'verify' => (bool) $provider['ssl_verify'],
                ])
                ->withToken((string) $provider['api_key'])
                ->timeout(25)
                ->post("{$provider['base_url']}/chat/completions", [
                    'model' => $provider['model'],
                    'temperature' => 0.3,
                    'messages' => $this->buildMessages($user, $message, $history, $context),
                    'response_format' => ['type' => 'json_object'],
                ]);
        } catch (Throwable $e) {
            $elapsedMs = (int) round((microtime(true) - $startedAt) * 1000);
            Log::error('chatbot.request_exception', [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'provider' => $provider['name'],
                'elapsed_ms' => $elapsedMs,
                'error' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'message' => ['Chatbot service is temporarily unavailable. Please try again shortly.'],
            ]);
        }

        $elapsedMs = (int) round((microtime(true) - $startedAt) * 1000);

        if (!$response->successful()) {
            Log::warning('chatbot.request_failed', [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'provider' => $provider['name'],
                'status' => $response->status(),
                'elapsed_ms' => $elapsedMs,
                'body' => mb_substr((string) $response->body(), 0, 500),
            ]);

            throw ValidationException::withMessages([
                'message' => ['Chatbot service is temporarily unavailable. Please try again shortly.'],
            ]);
        }

        $rawContent = (string) data_get($response->json(), 'choices.0.message.content', '{}');
        $decoded = json_decode($rawContent, true);
        if (!is_array($decoded)) {
            $decoded = [];
        }

        $result = [
            'reply' => $this->cleanReply((string) ($decoded['reply'] ?? 'Sorry, I could not generate a response.')),
            'intent' => $this->sanitizeIntent((string) ($decoded['intent'] ?? 'general_support')),
            'sources' => $this->normalizeStringList($decoded['sources'] ?? []),
            'suggestedActions' => $this->normalizeStringList($decoded['suggestedActions'] ?? []),
        ];

        Log::info('chatbot.request_completed', [
            'request_id' => $requestId,
            'user_id' => $user->id,
            'provider' => $provider['name'],
            'elapsed_ms' => $elapsedMs,
            'input_length' => mb_strlen($message),
            'prompt_tokens' => data_get($response->json(), 'usage.prompt_tokens'),
            'completion_tokens' => data_get($response->json(), 'usage.completion_tokens'),
            'total_tokens' => data_get($response->json(), 'usage.total_tokens'),
            'intent' => $result['intent'],
        ]);

        $this->storeConversationTurn($user, $message, $result['reply']);

        return $result;
    }

    public function history(User $user): array
    {
        return ChatbotMessage::query()
            ->where('user_id', $user->id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->limit(200)
            ->get(['id', 'role', 'content', 'created_at'])
            ->toArray();
    }

    public function clearHistory(User $user): void
    {
        ChatbotMessage::query()->where('user_id', $user->id)->delete();
    }

    private function resolveProvider(): array
    {
        $provider = strtolower((string) config('chatbot.provider', 'openai'));

        if ($provider === 'groq') {
            $apiKey = (string) config('services.groq.api_key', '');
            if ($apiKey === '') {
                throw ValidationException::withMessages([
                    'message' => ['Groq API key is not configured on the server.'],
                ]);
            }

            return [
                'name' => 'groq',
                'api_key' => $apiKey,
                'model' => (string) config('services.groq.model', 'llama-3.1-8b-instant'),
                'base_url' => rtrim((string) config('services.groq.base_url', 'https://api.groq.com/openai/v1'), '/'),
                'ssl_verify' => (bool) config('services.groq.ssl_verify', true),
            ];
        }

        $apiKey = (string) config('services.openai.api_key', '');
        if ($apiKey === '') {
            throw ValidationException::withMessages([
                'message' => ['OpenAI API key is not configured on the server.'],
            ]);
        }

        return [
            'name' => 'openai',
            'api_key' => $apiKey,
            'model' => (string) config('services.openai.model', 'gpt-4o-mini'),
            'base_url' => 'https://api.openai.com/v1',
            'ssl_verify' => (bool) config('services.openai.ssl_verify', true),
        ];
    }

    private function buildMessages(User $user, string $message, array $history, array $context): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => $this->systemPrompt(),
            ],
            [
                'role' => 'system',
                'content' => json_encode([
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'context' => Arr::only($context, ['screen', 'courseId', 'courseTitle']),
                ], JSON_UNESCAPED_SLASHES),
            ],
        ];

        foreach ($history as $turn) {
            $messages[] = $turn;
        }

        $messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        return $messages;
    }

    private function normalizeMessage(string $message): string
    {
        $normalized = trim(strip_tags($message));
        if ($normalized === '') {
            throw ValidationException::withMessages([
                'message' => ['Message is required.'],
            ]);
        }

        $maxChars = max(200, (int) config('chatbot.max_input_chars', 1200));
        if (mb_strlen($normalized) > $maxChars) {
            throw ValidationException::withMessages([
                'message' => ["Message must be {$maxChars} characters or fewer."],
            ]);
        }

        return $normalized;
    }

    private function normalizeHistory(mixed $history): array
    {
        if (!is_array($history)) {
            return [];
        }

        $maxHistory = max(0, (int) config('chatbot.max_history_messages', 8));
        $allowedRoles = ['user', 'assistant'];
        $trimmed = array_slice($history, -$maxHistory);

        $normalized = [];
        foreach ($trimmed as $turn) {
            if (!is_array($turn)) {
                continue;
            }

            $role = (string) ($turn['role'] ?? '');
            $content = trim(strip_tags((string) ($turn['content'] ?? '')));
            if (!in_array($role, $allowedRoles, true) || $content === '') {
                continue;
            }

            $normalized[] = [
                'role' => $role,
                'content' => mb_substr($content, 0, 1000),
            ];
        }

        return $normalized;
    }

    private function resolveHistory(User $user, mixed $historyPayload): array
    {
        $payloadHistory = $this->normalizeHistory($historyPayload);
        if ($payloadHistory !== []) {
            return $payloadHistory;
        }

        $maxHistory = max(0, (int) config('chatbot.max_history_messages', 8));
        if ($maxHistory === 0) {
            return [];
        }

        return ChatbotMessage::query()
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->latest('id')
            ->limit($maxHistory)
            ->get(['role', 'content'])
            ->reverse()
            ->values()
            ->map(static function (ChatbotMessage $message): array {
                return [
                    'role' => $message->role,
                    'content' => mb_substr(trim(strip_tags($message->content)), 0, 1000),
                ];
            })
            ->all();
    }

    private function storeConversationTurn(User $user, string $userMessage, string $assistantReply): void
    {
        ChatbotMessage::query()->create([
            'user_id' => $user->id,
            'role' => 'user',
            'content' => $userMessage,
        ]);

        ChatbotMessage::query()->create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'content' => $assistantReply,
        ]);
    }

    private function normalizeStringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $item) {
            if (!is_string($item)) {
                continue;
            }
            $clean = trim(strip_tags($item));
            if ($clean !== '') {
                $items[] = mb_substr($clean, 0, 120);
            }
            if (count($items) >= 4) {
                break;
            }
        }

        return $items;
    }

    private function sanitizeIntent(string $intent): string
    {
        $normalized = strtolower(trim($intent));
        $allowed = ['faq', 'learning_help', 'support_handoff', 'general_support'];

        return in_array($normalized, $allowed, true) ? $normalized : 'general_support';
    }

    private function cleanReply(string $reply): string
    {
        $clean = trim(strip_tags($reply));
        return $clean !== '' ? mb_substr($clean, 0, 2000) : 'Sorry, I could not generate a response.';
    }

    private function systemPrompt(): string
    {
        return <<<PROMPT
You are EduWrap Assistant for a learning platform.
Primary roles:
1) Platform FAQ support (courses, enrollments, payments, account basics).
2) Learning assistant (explain topics simply and give practical study steps).

Rules:
- Stay within EduWrap scope and education help.
- If information is uncertain (pricing, policy, account-specific data), say you are not certain and suggest checking official support/profile screens.
- Do not provide harmful, unsafe, illegal, or abusive guidance.
- Keep replies concise and actionable.
- Ask one clarifying question when user intent is ambiguous.

Return strict JSON with this shape:
{
  "reply": "assistant text",
  "intent": "faq | learning_help | support_handoff | general_support",
  "sources": ["optional short source label"],
  "suggestedActions": ["optional short next action"]
}
PROMPT;
    }
}
