<?php

namespace Byron\AiGit\Services;

use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;

class AIService
{
    protected string $provider;
    protected string $apiKey;
    protected string $model;
    protected float $temperature;
    protected int $maxTokens;
    protected Client $client;

    public function __construct()
    {
        $this->provider = Config::get('openai.provider', 'openai');
        $this->client = new Client();
        
        // Load provider-specific configuration
        if ($this->provider === 'claude') {
            $this->apiKey = Config::get('openai.claude.api_key');
            $this->model = Config::get('openai.claude.model', 'claude-3-opus-20240229');
            $this->temperature = (float) Config::get('openai.claude.temperature', 0.7);
            $this->maxTokens = (int) Config::get('openai.claude.max_tokens', 500);
            
            if (empty($this->apiKey)) {
                throw new \RuntimeException('Claude API key is not set. Please set the CLAUDE_API_KEY environment variable.');
            }
        } else {
            // Default to OpenAI
            $this->apiKey = Config::get('openai.openai.api_key');
            $this->model = Config::get('openai.openai.model', 'gpt-4o');
            $this->temperature = (float) Config::get('openai.openai.temperature', 0.7);
            $this->maxTokens = (int) Config::get('openai.openai.max_tokens', 500);
            
            if (empty($this->apiKey)) {
                throw new \RuntimeException('OpenAI API key is not set. Please set the OPENAI_API_KEY environment variable.');
            }
        }
    }

    /**
     * Generate a commit message based on git diff
     *
     * @param string $diff The git diff content
     * @return string The generated commit message
     */
    public function generateCommitMessage(string $diff): string
    {
        $prompt = Config::get('openai.commit_message_prompt');
        
        return $this->callAI($prompt, $diff);
    }

    /**
     * Generate a PR summary based on commits
     *
     * @param string $commits The commit history
     * @param bool $isMainBranch Whether this is for a main branch merge
     * @return string The generated PR summary
     */
    public function generatePRSummary(string $commits, bool $isMainBranch = false): string
    {
        $prompt = $isMainBranch 
            ? Config::get('openai.pr_main_summary_prompt')
            : Config::get('openai.pr_summary_prompt');
        
        $maxTokens = $this->maxTokens * 2; // Allow more tokens for PR summaries
        
        return $this->callAI($prompt, $commits, $maxTokens);
    }
    
    /**
     * Call the AI API (OpenAI or Claude)
     *
     * @param string $systemPrompt The system prompt
     * @param string $userContent The user content
     * @param int|null $maxTokens Maximum tokens to generate
     * @return string The generated text
     */
    protected function callAI(string $systemPrompt, string $userContent, ?int $maxTokens = null): string
    {
        if ($this->provider === 'claude') {
            return $this->callClaude($systemPrompt, $userContent, $maxTokens);
        } else {
            return $this->callOpenAI($systemPrompt, $userContent, $maxTokens);
        }
    }
    
    /**
     * Call the OpenAI API
     *
     * @param string $systemPrompt The system prompt
     * @param string $userContent The user content
     * @param int|null $maxTokens Maximum tokens to generate
     * @return string The generated text
     */
    protected function callOpenAI(string $systemPrompt, string $userContent, ?int $maxTokens = null): string
    {
        try {
            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'temperature' => $this->temperature,
                    'max_tokens' => $maxTokens ?? $this->maxTokens,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userContent],
                    ],
                ],
            ]);
            
            $statusCode = $response->getStatusCode();
            
            if ($statusCode !== 200) {
                throw new \RuntimeException('OpenAI API request failed with status code ' . $statusCode);
            }
            
            $data = json_decode($response->getBody(), true);
            
            if (empty($data['choices'][0]['message']['content'])) {
                throw new \RuntimeException('OpenAI API returned an empty response.');
            }
            
            return trim($data['choices'][0]['message']['content']);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('OpenAI API request failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Call the Claude API
     *
     * @param string $systemPrompt The system prompt
     * @param string $userContent The user content
     * @param int|null $maxTokens Maximum tokens to generate
     * @return string The generated text
     */
    protected function callClaude(string $systemPrompt, string $userContent, ?int $maxTokens = null): string
    {
        try {
            $response = $this->client->post('https://api.anthropic.com/v1/messages', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'anthropic-version' => '2023-06-01',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'max_tokens' => $maxTokens ?? $this->maxTokens,
                    'temperature' => $this->temperature,
                    'system' => $systemPrompt,
                    'messages' => [
                        ['role' => 'user', 'content' => $userContent],
                    ],
                ],
            ]);
            
            $statusCode = $response->getStatusCode();
            
            if ($statusCode !== 200) {
                throw new \RuntimeException('Claude API request failed with status code ' . $statusCode);
            }
            
            $data = json_decode($response->getBody(), true);
            
            if (empty($data['content'][0]['text'])) {
                throw new \RuntimeException('Claude API returned an empty response.');
            }
            
            return trim($data['content'][0]['text']);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Claude API request failed: ' . $e->getMessage());
        }
    }
} 