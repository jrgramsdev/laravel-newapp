<?php

namespace App\Services\Llm;

/**
 * Provider-agnostic result of a single completion.
 *
 * Anthropic and OpenAI disagree on response shape and on what they call the
 * token counts. Normalising here means the job, the model, and the tests never
 * have to know which provider ran.
 */
final readonly class LlmResponse
{
    public function __construct(
        public string $text,
        public string $provider,
        public string $model,
        public ?int $inputTokens = null,
        public ?int $outputTokens = null,
        public ?int $durationMs = null,
    ) {}
}
