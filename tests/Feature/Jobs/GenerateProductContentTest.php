<?php

namespace Tests\Feature\Jobs;

use App\Enums\GenerationStatus;
use App\Jobs\GenerateProductContent;
use App\Models\ContentGeneration;
use App\Services\Llm\FakeLlmClient;
use App\Services\Llm\LlmClient;
use App\Services\Llm\LlmException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateProductContentTest extends TestCase
{
    use RefreshDatabase;

    private FakeLlmClient $llm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->llm = new FakeLlmClient;
        $this->app->instance(LlmClient::class, $this->llm);
    }

    public function test_it_stores_the_result_and_provider_metadata(): void
    {
        $this->llm->respondWith('A copper kettle that heats fast and pours clean.');
        $generation = ContentGeneration::factory()->create();

        (new GenerateProductContent($generation))->handle($this->llm);

        $generation->refresh();
        $this->assertSame(GenerationStatus::Completed, $generation->status);
        $this->assertSame('A copper kettle that heats fast and pours clean.', $generation->result);
        $this->assertSame('fake', $generation->provider);
        $this->assertSame(128, $generation->output_tokens);
        $this->assertNotNull($generation->completed_at);
        $this->assertNull($generation->error);
    }

    public function test_it_sends_the_stored_prompt_to_the_provider(): void
    {
        $generation = ContentGeneration::factory()->create(['prompt' => 'Write copy for a copper kettle.']);

        (new GenerateProductContent($generation))->handle($this->llm);

        $this->assertSame(['Write copy for a copper kettle.'], $this->llm->prompts);
    }

    public function test_a_retryable_failure_is_rethrown_so_the_queue_retries_it(): void
    {
        $this->llm->failWith(new LlmException('Rate limited', retryable: true));
        $generation = ContentGeneration::factory()->create();

        $this->expectException(LlmException::class);

        try {
            (new GenerateProductContent($generation))->handle($this->llm);
        } finally {
            // Still mid-flight — marking it failed here would tell the merchant
            // it is over while the queue is about to try again.
            $this->assertSame(GenerationStatus::Processing, $generation->refresh()->status);
        }
    }

    public function test_a_non_retryable_failure_is_recorded_without_burning_retries(): void
    {
        $this->llm->failWith(new LlmException('The provider declined this request', retryable: false));
        $generation = ContentGeneration::factory()->create();

        (new GenerateProductContent($generation))->handle($this->llm);

        $generation->refresh();
        $this->assertSame(GenerationStatus::Failed, $generation->status);
        $this->assertSame('The provider declined this request', $generation->error);
        $this->assertNotNull($generation->completed_at);
    }

    public function test_the_failed_hook_closes_out_a_generation_left_processing(): void
    {
        $generation = ContentGeneration::factory()->create(['status' => GenerationStatus::Processing]);

        (new GenerateProductContent($generation))->failed(new LlmException('Rate limited', retryable: true));

        $this->assertSame(GenerationStatus::Failed, $generation->refresh()->status);
    }

    public function test_the_failed_hook_does_not_overwrite_a_recorded_result(): void
    {
        $generation = ContentGeneration::factory()->completed()->create();
        $result = $generation->result;

        (new GenerateProductContent($generation))->failed(new LlmException('late failure'));

        $generation->refresh();
        $this->assertSame(GenerationStatus::Completed, $generation->status);
        $this->assertSame($result, $generation->result);
    }
}
