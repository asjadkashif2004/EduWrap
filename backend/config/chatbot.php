<?php

return [
    'enabled' => filter_var(env('CHATBOT_ENABLED', true), FILTER_VALIDATE_BOOL),
    'provider' => env('CHATBOT_PROVIDER', 'openai'),
    'rate_limit_per_minute' => (int) env('CHATBOT_RATE_LIMIT_PER_MINUTE', 20),
    'max_input_chars' => (int) env('CHATBOT_MAX_INPUT_CHARS', 1200),
    'max_history_messages' => (int) env('CHATBOT_MAX_HISTORY_MESSAGES', 8),
];
