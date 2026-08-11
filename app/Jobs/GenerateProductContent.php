<?php

namespace App\Jobs;

use App\Models\ContentGeneration;
use App\Services\Llm\LlmClient;
use App\Services\Llm\LlmException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class GenerateProductContent implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * Provider calls are slow by nature; the default 60s timeout kills a
     * legitimate long completion.
     */
    public int $timeout = 180;

    public function __construct(public ContentGeneration $generation) {}

    /**
     * Back off between retries so a rate limit has time to clear.
     *
     * @return list<int>
     */
    public function backoff(): array
    {
        return [5, 20];
    }

    public function handle(LlmClient $llm): void
    {
        $this->generation->markProcessing();

        try {
            $response = $llm->complete(
                $this->generation->prompt,
                $this->generation->type->maxTokens(),
            );
        } catch (LlmException $e) {
            // A non-retryable failure — a malformed prompt, a refusal — fails
            // the same way on every attempt, so burning the remaining tries
            // just delays the error the merchant is waiting on.
            if (! $e->retryable) {
                $this->generation->markFailed($e->getMessage());
                $this->fail($e);

                return;
            }

            throw $e;
        }

        $this->generation->markCompleted($response);
    }

    /**
     * Runs after the final attempt (or an explicit fail()), so the row never
     * sits in "processing" forever with the merchant polling it.
     */
    public function failed(?Throwable $e): void
    {
        $this->generation->refresh();

        if ($this->generation->status->isTerminal()) {
            return;
        }

        $this->generation->markFailed($e?->getMessage() ?? 'The job failed without an exception.');
    }
}
