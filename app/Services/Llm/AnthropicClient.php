<?php

namespace App\Services\Llm;

use Anthropic\Client;
use Anthropic\Core\Exceptions\APIConnectionException;
use Anthropic\Core\Exceptions\APIStatusException;
use Anthropic\Core\Exceptions\APITimeoutException;
use Anthropic\Messages\TextBlock;

class AnthropicClient implements LlmClient
{
    public function __construct(
        private readonly Client $client,
        private readonly string $model,
        private readonly string $effort,
    ) {}

    public function complete(string $prompt, int $maxTokens): LlmResponse
    {
        $startedAt = hrtime(true);

        try {
            $message = $this->client->messages->create(
                maxTokens: $maxTokens,
                messages: [['role' => 'user', 'content' => $prompt]],
                model: $this->model,
                // Thinking is left unset: it defaults to adaptive on current
                // models, and effort is the lever for spend. maxTokens caps
                // thinking and visible output together, so GenerationType
                // budgets carry headroom above the copy itself.
                outputConfig: ['effort' => $this->effort],
            );
        } catch (APITimeoutException|APIConnectionException $e) {
            throw new LlmException("Could not reach the provider: {$e->getMessage()}", retryable: true);
        } catch (APIStatusException $e) {
            throw LlmException::fromStatus($e->status ?? 0, $e->getMessage());
        }

        // A safety classifier can decline the request: HTTP 200, empty or
        // partial content, and stopReason 'refusal'. Reading content blindly
        // here would yield an empty result that looks like a success.
        if ($message->stopReason === 'refusal') {
            throw new LlmException(
                'The provider declined this request'
                    .($message->stopDetails?->category ? " ({$message->stopDetails->category})" : ''),
                retryable: false,
            );
        }

        $text = $this->extractText($message->content);

        if ($text === '') {
            throw new LlmException(
                "The provider returned no text (stop reason: {$message->stopReason}).",
                retryable: $message->stopReason === 'max_tokens',
            );
        }

        return new LlmResponse(
            text: $text,
            provider: 'anthropic',
            model: $message->model,
            inputTokens: $message->usage->inputTokens,
            outputTokens: $message->usage->outputTokens,
            durationMs: (int) ((hrtime(true) - $startedAt) / 1_000_000),
        );
    }

    /**
     * Concatenate the text blocks, ignoring thinking blocks and anything else
     * the model may return alongside them.
     *
     * @param  array<int, mixed>  $content
     */
    private function extractText(array $content): string
    {
        $parts = [];

        foreach ($content as $block) {
            if ($block instanceof TextBlock) {
                $parts[] = $block->text;
            }
        }

        return trim(implode('', $parts));
    }
}
