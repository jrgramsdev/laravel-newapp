<?php

namespace Tests\Unit;

use App\Enums\GenerationType;
use App\Models\Product;
use App\Services\Llm\PromptBuilder;
use PHPUnit\Framework\TestCase;

class PromptBuilderTest extends TestCase
{
    private PromptBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new PromptBuilder;
    }

    public function test_it_includes_every_fact_the_merchant_supplied(): void
    {
        $product = new Product([
            'name' => 'Copper kettle',
            'source_url' => 'https://example.com/kettle',
            'notes' => 'Gooseneck spout',
        ]);

        $prompt = $this->builder->build($product, GenerationType::ProductDescription);

        $this->assertStringContainsString('Copper kettle', $prompt);
        $this->assertStringContainsString('https://example.com/kettle', $prompt);
        $this->assertStringContainsString('Gooseneck spout', $prompt);
    }

    public function test_it_omits_optional_fields_rather_than_sending_empty_labels(): void
    {
        $product = new Product(['name' => 'Copper kettle']);

        $prompt = $this->builder->build($product, GenerationType::ProductDescription);

        $this->assertStringContainsString('Copper kettle', $prompt);
        $this->assertStringNotContainsString('Source URL:', $prompt);
        $this->assertStringNotContainsString('Merchant notes:', $prompt);
    }

    public function test_each_type_asks_for_something_different(): void
    {
        $product = new Product(['name' => 'Copper kettle']);

        $prompts = array_map(
            fn (GenerationType $type): string => $this->builder->build($product, $type),
            GenerationType::cases(),
        );

        $this->assertCount(count(GenerationType::cases()), array_unique($prompts));
    }

    public function test_the_seo_prompt_carries_the_length_limit(): void
    {
        $prompt = $this->builder->build(new Product(['name' => 'X']), GenerationType::SeoMeta);

        $this->assertStringContainsString('160 characters', $prompt);
    }

    public function test_every_prompt_suppresses_preamble(): void
    {
        foreach (GenerationType::cases() as $type) {
            $prompt = $this->builder->build(new Product(['name' => 'X']), $type);

            $this->assertStringContainsString('Return only the copy itself', $prompt);
        }
    }
}
