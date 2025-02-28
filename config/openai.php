<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your AI provider settings. You can use the env
    | variables to set your API keys and other configuration options.
    |
    */

    'provider' => env('AI_PROVIDER', 'openai'), // 'openai' or 'claude'
    
    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o'),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 500),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Claude Configuration
    |--------------------------------------------------------------------------
    */
    'claude' => [
        'api_key' => env('CLAUDE_API_KEY'),
        'model' => env('CLAUDE_MODEL', 'claude-3-opus-20240229'),
        'temperature' => env('CLAUDE_TEMPERATURE', 0.7),
        'max_tokens' => env('CLAUDE_MAX_TOKENS', 500),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Prompts Configuration
    |--------------------------------------------------------------------------
    */
    'commit_message_prompt' => env('AI_COMMIT_MESSAGE_PROMPT', 
        "Generate a clear, conventional commit message based on the following git diff. " .
        "Follow the Conventional Commits specification (https://www.conventionalcommits.org/). " .
        "The message should have a type (feat, fix, docs, style, refactor, perf, test, chore), " .
        "an optional scope, and a concise description. " .
        "If there are breaking changes, include a '!' after the type/scope. " .
        "Keep the message under 100 characters. " .
        "Return ONLY the commit message without any additional text or formatting."
    ),
    
    'pr_summary_prompt' => env('AI_PR_SUMMARY_PROMPT',
        "Generate a comprehensive pull request description based on the following commits. " .
        "Include: \n" .
        "1. A clear title summarizing the changes\n" .
        "2. A brief overview of what was changed and why\n" .
        "3. A bulleted list of key changes\n" .
        "4. Any breaking changes or important notes for reviewers\n" .
        "Format the response in Markdown."
    ),
    
    'pr_main_summary_prompt' => env('AI_PR_MAIN_SUMMARY_PROMPT',
        "Generate a comprehensive release summary for merging to the main branch. " .
        "Based on the following list of merged PRs, create: \n" .
        "1. A clear release title\n" .
        "2. A summary of the major changes in this release\n" .
        "3. A categorized list of changes (Features, Bug Fixes, Performance, etc.)\n" .
        "4. Include links to all the merged PRs\n" .
        "Format the response in Markdown."
    ),
]; 