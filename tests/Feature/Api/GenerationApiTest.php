<?php

namespace Tests\Feature\Api;

use App\Enums\GenerationStatus;
use App\Enums\GenerationType;
use App\Jobs\GenerateProductContent;
use App\Models\ContentGeneration;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GenerationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_requesting_a_generation_queues_a_job_and_returns_202(): void
    {
        Queue::fake();
        $product = Product::factory()->create();

        $response = $this->postJson("/api/v1/products/{$product->id}/generations", [
            'type' => 'product_description',
        ]);

        $response->assertAccepted()
            ->assertJsonPath('data.status', 'queued')
            ->assertJsonPath('data.is_complete', false)
            ->assertJsonPath('data.result', null);

        Queue::assertPushed(GenerateProductContent::class, function (GenerateProductContent $job) use ($response): bool {
            return $job->generation->id === $response->json('data.id');
        });
    }

    public function test_the_prompt_is_stored_with_the_generation(): void
    {
        Queue::fake();
        $product = Product::factory()->create(['name' => 'Copper kettle']);

        $this->postJson("/api/v1/products/{$product->id}/generations", ['type' => 'ad_copy']);

        $generation = ContentGeneration::sole();
        $this->assertStringContainsString('Copper kettle', $generation->prompt);
        $this->assertSame(GenerationType::AdCopy, $generation->type);
    }

    public function test_it_rejects_an_unknown_generation_type(): void
    {
        Queue::fake();
        $product = Product::factory()->create();

        $this->postJson("/api/v1/products/{$product->id}/generations", ['type' => 'haiku'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('type');

        Queue::assertNothingPushed();
    }

    public function test_it_rejects_a_missing_generation_type(): void
    {
        Queue::fake();
        $product = Product::factory()->create();

        $this->postJson("/api/v1/products/{$product->id}/generations", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('type');
    }

    public function test_polling_a_pending_generation_reports_it_incomplete(): void
    {
        $generation = ContentGeneration::factory()->create(['status' => GenerationStatus::Processing]);

        $this->getJson("/api/v1/generations/{$generation->id}")
            ->assertOk()
            ->assertJsonPath('data.status', 'processing')
            ->assertJsonPath('data.is_complete', false);
    }

    public function test_polling_a_finished_generation_returns_the_result_and_usage(): void
    {
        $generation = ContentGeneration::factory()->completed()->create();

        $this->getJson("/api/v1/generations/{$generation->id}")
            ->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.is_complete', true)
            ->assertJsonPath('data.result', $generation->result)
            ->assertJsonPath('data.output_tokens', 240);
    }

    public function test_a_failed_generation_is_complete_and_carries_its_error(): void
    {
        $generation = ContentGeneration::factory()->failed()->create();

        $this->getJson("/api/v1/generations/{$generation->id}")
            ->assertOk()
            ->assertJsonPath('data.is_complete', true)
            ->assertJsonPath('data.error', 'Provider returned HTTP 429');
    }
}
