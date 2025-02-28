<?php

return [
    'ai_provider' => env('AI_PROVIDER', 'openai'),

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4-turbo-preview'),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 500),
    ],

    'claude' => [
        'api_key' => env('CLAUDE_API_KEY'),
        'model' => env('CLAUDE_MODEL', 'claude-3-opus-20240229'),
        'temperature' => env('CLAUDE_TEMPERATURE', 0.7),
        'max_tokens' => env('CLAUDE_MAX_TOKENS', 500),
    ],
]; 