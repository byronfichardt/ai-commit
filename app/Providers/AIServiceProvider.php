<?php

namespace App\Providers;

use App\Services\AIService;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AIService::class, function ($app) {
            return new AIService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/openai.php' => $this->app->configPath('openai.php'),
        ], 'config');
    }
} 