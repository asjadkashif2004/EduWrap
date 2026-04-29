<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ChatbotController extends Controller
{
    public function __construct(
        private readonly ChatbotService $chatbotService,
        private readonly RateLimiter $rateLimiter,
    ) {
    }

    public function message(Request $request)
    {
        if (!config('chatbot.enabled', true)) {
            return response()->json([
                'message' => 'Chatbot is currently unavailable.',
            ], 503);
        }

        $validated = $request->validate([
            'message' => ['required', 'string'],
            'history' => ['sometimes', 'array'],
            'history.*.role' => ['required_with:history', 'string'],
            'history.*.content' => ['required_with:history', 'string'],
            'context' => ['sometimes', 'array'],
            'context.screen' => ['sometimes', 'string', 'max:80'],
            'context.courseId' => ['sometimes', 'integer'],
            'context.courseTitle' => ['sometimes', 'string', 'max:200'],
        ]);

        $user = $request->user();
        $limit = max(1, (int) config('chatbot.rate_limit_per_minute', 20));
        $key = "chatbot:{$user->id}";

        if ($this->rateLimiter->tooManyAttempts($key, $limit)) {
            throw ValidationException::withMessages([
                'message' => ['Too many chatbot requests. Please wait a moment and try again.'],
            ]);
        }

        $this->rateLimiter->hit($key, 60);

        $result = $this->chatbotService->respond($user, $validated);

        return response()->json($result);
    }

    public function history(Request $request)
    {
        return response()->json($this->chatbotService->history($request->user()));
    }

    public function clearHistory(Request $request)
    {
        $this->chatbotService->clearHistory($request->user());

        return response()->json([
            'message' => 'Chatbot history cleared.',
        ]);
    }
}
