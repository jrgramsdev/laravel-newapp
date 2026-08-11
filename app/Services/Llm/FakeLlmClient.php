<?php

namespace App\Services\Llm;

/**
 * Deterministic stand-in for a real provider.
 *
 * Two jobs: let the app run end to end without an API key, and give the test
 * suite a client that never costs money, never varies, and never needs the
 * network. Tests that need a failure path call `failWith()`.
 */
class FakeLlmClient implements LlmClient
{
    /** @var list<string> */
    public array $prompts = [];

    private ?LlmException $failure = null;

    private ?string $cannedText = null;

    public function failWith(LlmException $exception): self
    {
        $this->failure = $exception;

        return $this;
    }

    public function respondWith(string $text): self
    {
        $this->cannedText = $text;

        return $this;
    }

    public function complete(string $prompt, int $maxTokens): LlmResponse
    {
        $this->prompts[] = $prompt;

        if ($this->failure !== null) {
            throw $this->failure;
        }

        return new LlmResponse(
            text: $this->cannedText ?? $this->syntheticCopy($prompt),
            provider: 'fake',
            model: 'fake-model',
            inputTokens: (int) ceil(mb_strlen($prompt) / 4),
            outputTokens: 128,
            durationMs: 5,
        );
    }

    /**
     * Echo enough of the prompt back that a human clicking through the UI
     * without an API key can still tell the pipeline wired up correctly.
     */
    private function syntheticCopy(string $prompt): string
    {
        $subject = str_contains($prompt, 'Product name:')
            ? trim(explode("\n", explode('Product name:', $prompt, 2)[1], 2)[0])
            : 'this product';

        return "[fake provider] Generated copy for {$subject}. "
            .'Set ANTHROPIC_API_KEY and LLM_DRIVER=anthropic to generate real copy.';
    }
}
