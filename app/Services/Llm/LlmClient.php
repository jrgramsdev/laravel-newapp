<?php

namespace App\Services\Llm;

interface LlmClient
{
    /**
     * Run a single completion.
     *
     * @throws LlmException when the provider errors, times out, or returns a
     *                      response this client cannot parse.
     */
    public function complete(string $prompt, int $maxTokens): LlmResponse;
}
