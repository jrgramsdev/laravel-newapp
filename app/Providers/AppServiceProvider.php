<?php

namespace App\Providers;

use Anthropic\Client as Anthropic;
use App\Services\Llm\AnthropicClient;
use App\Services\Llm\FakeLlmClient;
use App\Services\Llm\LlmClient;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LlmClient::class, function (): LlmClient {
            $driver = config('llm.driver');

            return match ($driver) {
                'fake' => new FakeLlmClient,
                'anthropic' => $this->makeAnthropicClient(),
                default => throw new InvalidArgumentException("Unsupported LLM driver [{$driver}]."),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    private function makeAnthropicClient(): AnthropicClient
    {
        $apiKey = config('llm.anthropic.api_key');

        if (blank($apiKey)) {
            throw new InvalidArgumentException(
                'LLM_DRIVER is "anthropic" but ANTHROPIC_API_KEY is not set.'
            );
        }

        return new AnthropicClient(
            client: new Anthropic(apiKey: $apiKey),
            model: config('llm.anthropic.model'),
            effort: config('llm.anthropic.effort'),
        );
    }
}
