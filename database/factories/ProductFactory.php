<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'source_url' => fake()->url(),
            'notes' => fake()->sentence(),
        ];
    }

    public function withoutContext(): static
    {
        return $this->state(fn (): array => [
            'source_url' => null,
            'notes' => null,
        ]);
    }
}
