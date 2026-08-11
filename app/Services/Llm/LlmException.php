<?php

namespace App\Services\Llm;

use RuntimeException;

class LlmException extends RuntimeException
{
    /**
     * Whether retrying the same request could plausibly succeed.
     *
     * Rate limits, timeouts and 5xx are worth another attempt. A 400 from a
     * malformed prompt will fail identically every time, so the job should
     * stop rather than burn its remaining tries.
     */
    public function __construct(
        string $message,
        public readonly bool $retryable = false,
        public readonly ?int $statusCode = null,
    ) {
        parent::__construct($message);
    }

    public static function fromStatus(int $status, string $body): self
    {
        $retryable = $status === 429 || $status >= 500;

        return new self(
            "Provider returned HTTP {$status}: ".mb_substr($body, 0, 500),
            retryable: $retryable,
            statusCode: $status,
        );
    }
}
