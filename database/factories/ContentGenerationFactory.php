<?php

namespace Database\Factories;

use App\Enums\GenerationStatus;
use App\Enums\GenerationType;
use App\Models\ContentGeneration;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContentGeneration>
 */
class ContentGenerationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'type' => fake()->randomElement(GenerationType::cases()),
            'status' => GenerationStatus::Queued,
            'prompt' => fake()->paragraph(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => GenerationStatus::Completed,
            'result' => fake()->paragraph(),
            'provider' => 'fake',
            'model' => 'fake-model',
            'input_tokens' => 120,
            'output_tokens' => 240,
            'duration_ms' => 900,
            'completed_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (): array => [
            'status' => GenerationStatus::Failed,
            'error' => 'Provider returned HTTP 429',
            'completed_at' => now(),
        ]);
    }
}
