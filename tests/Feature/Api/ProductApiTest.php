<?php

namespace Tests\Feature\Api;

use App\Models\ContentGeneration;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_products_newest_first_with_their_generations(): void
    {
        $older = Product::factory()->create(['created_at' => now()->subDay()]);
        $newer = Product::factory()->create();
        ContentGeneration::factory()->completed()->for($newer)->create();

        $response = $this->getJson('/api/v1/products');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $newer->id)
            ->assertJsonPath('data.1.id', $older->id)
            ->assertJsonCount(1, 'data.0.generations')
            ->assertJsonCount(0, 'data.1.generations');
    }

    public function test_it_creates_a_product(): void
    {
        $response = $this->postJson('/api/v1/products', [
            'name' => 'Ceramic pour-over dripper',
            'source_url' => 'https://example.com/dripper',
            'notes' => 'Single origin, 1-2 cups',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Ceramic pour-over dripper');

        $this->assertDatabaseHas('products', ['name' => 'Ceramic pour-over dripper']);
    }

    public function test_it_creates_a_product_with_only_a_name(): void
    {
        $this->postJson('/api/v1/products', ['name' => 'Minimal'])
            ->assertCreated()
            ->assertJsonPath('data.source_url', null)
            ->assertJsonPath('data.notes', null);
    }

    public function test_it_rejects_a_product_without_a_name(): void
    {
        $this->postJson('/api/v1/products', ['source_url' => 'https://example.com'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_it_rejects_a_malformed_source_url(): void
    {
        $this->postJson('/api/v1/products', [
            'name' => 'Thing',
            'source_url' => 'not-a-url',
        ])->assertUnprocessable()->assertJsonValidationErrors('source_url');
    }

    public function test_it_shows_a_single_product(): void
    {
        $product = Product::factory()->create();

        $this->getJson("/api/v1/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_it_404s_for_an_unknown_product(): void
    {
        $this->getJson('/api/v1/products/999')->assertNotFound();
    }

    public function test_deleting_a_product_removes_its_generations(): void
    {
        $product = Product::factory()->create();
        $generation = ContentGeneration::factory()->for($product)->create();

        $this->deleteJson("/api/v1/products/{$product->id}")->assertNoContent();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('content_generations', ['id' => $generation->id]);
    }
}
