<?php

namespace Tests\Feature;

use App\Models\ChatbotMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatbotMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_send_chat_message(): void
    {
        config()->set('chatbot.enabled', true);
        config()->set('chatbot.provider', 'openai');
        config()->set('services.openai.api_key', 'test-key');
        config()->set('services.openai.model', 'gpt-4o-mini');

        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'reply' => 'Try 30 minutes daily with active recall.',
                                'intent' => 'learning_help',
                                'sources' => ['Learning best-practice'],
                                'suggestedActions' => ['Create a 7-day schedule'],
                            ]),
                        ],
                    ],
                ],
                'usage' => [
                    'prompt_tokens' => 120,
                    'completion_tokens' => 35,
                    'total_tokens' => 155,
                ],
            ], 200),
        ]);

        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/chatbot/message', [
            'message' => 'Help me improve consistency in studying.',
            'history' => [
                ['role' => 'user', 'content' => 'I lose focus quickly'],
                ['role' => 'assistant', 'content' => 'Let us create a short routine.'],
            ],
            'context' => [
                'screen' => 'Dashboard',
            ],
        ]);

        $response->assertOk()
            ->assertJsonStructure(['reply', 'intent', 'sources', 'suggestedActions'])
            ->assertJson([
                'intent' => 'learning_help',
            ]);
    }

    public function test_chatbot_returns_503_when_disabled(): void
    {
        config()->set('chatbot.enabled', false);
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/chatbot/message', [
            'message' => 'Hello assistant',
        ]);

        $response->assertStatus(503);
    }

    public function test_chatbot_history_can_be_listed_and_cleared(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        ChatbotMessage::query()->create([
            'user_id' => $user->id,
            'role' => 'user',
            'content' => 'Hello',
        ]);
        ChatbotMessage::query()->create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'content' => 'Hi there',
        ]);

        $historyResponse = $this->getJson('/api/chatbot/history');
        $historyResponse->assertOk()
            ->assertJsonCount(2);

        $clearResponse = $this->deleteJson('/api/chatbot/history');
        $clearResponse->assertOk();

        $this->assertDatabaseCount('chatbot_messages', 0);
    }
}
