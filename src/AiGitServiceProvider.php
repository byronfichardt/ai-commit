<?php

namespace Byron\AiGit;

use Byron\AiGit\Commands\AiCommitCommand;
use Byron\AiGit\Commands\AiPrSummaryCommand;
use Byron\AiGit\Services\AIService;
use Illuminate\Support\ServiceProvider;

class AiGitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ai-git.php', 'ai-git');

        $this->app->singleton(AIService::class, function ($app) {
            return new AIService();
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ai-git.php' => config_path('ai-git.php'),
            ], 'config');

            $this->commands([
                AiCommitCommand::class,
                AiPrSummaryCommand::class,
            ]);
        }
    }
} 