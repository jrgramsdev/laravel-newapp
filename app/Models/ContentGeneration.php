<?php

namespace App\Models;

use App\Enums\GenerationStatus;
use App\Enums\GenerationType;
use App\Services\Llm\LlmResponse;
use Database\Factories\ContentGenerationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentGeneration extends Model
{
    /** @use HasFactory<ContentGenerationFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'status',
        'prompt',
        'result',
        'error',
        'provider',
        'model',
        'input_tokens',
        'output_tokens',
        'duration_ms',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => GenerationType::class,
            'status' => GenerationStatus::class,
            'completed_at' => 'datetime',
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'duration_ms' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function markProcessing(): void
    {
        $this->update(['status' => GenerationStatus::Processing]);
    }

    public function markCompleted(LlmResponse $response): void
    {
        $this->update([
            'status' => GenerationStatus::Completed,
            'result' => $response->text,
            'provider' => $response->provider,
            'model' => $response->model,
            'input_tokens' => $response->inputTokens,
            'output_tokens' => $response->outputTokens,
            'duration_ms' => $response->durationMs,
            'completed_at' => now(),
            'error' => null,
        ]);
    }

    public function markFailed(string $message): void
    {
        $this->update([
            'status' => GenerationStatus::Failed,
            // Provider errors can carry long payloads; the column is indexed by
            // nothing and read by humans, so cap it rather than store a wall.
            'error' => mb_substr($message, 0, 1000),
            'completed_at' => now(),
        ]);
    }
}
