<?php

namespace App\Services\Llm;

use App\Enums\GenerationType;
use App\Models\Product;

class PromptBuilder
{
    public function build(Product $product, GenerationType $type): string
    {
        $facts = ["Product name: {$product->name}"];

        if ($product->source_url) {
            $facts[] = "Source URL: {$product->source_url}";
        }

        if ($product->notes) {
            $facts[] = "Merchant notes: {$product->notes}";
        }

        return implode("\n\n", [
            $this->instruction($type),
            implode("\n", $facts),
            // Merchants paste this straight into a storefront, so anything the
            // model adds around the copy has to be stripped by hand.
            'Return only the copy itself, with no preamble, commentary, or surrounding quotes.',
        ]);
    }

    private function instruction(GenerationType $type): string
    {
        return match ($type) {
            GenerationType::ProductDescription => 'Write a product description for an e-commerce storefront. Two short paragraphs. Concrete and specific about what the product does for the buyer; no superlatives you cannot support from the facts below.',
            GenerationType::AdCopy => 'Write three short social ad variants for this product. One line each, numbered. Each should lead with a different angle.',
            GenerationType::TitleVariants => 'Write five product title variants, numbered, each under 70 characters and suitable for a storefront listing.',
            GenerationType::SeoMeta => 'Write a single SEO meta description under 160 characters. No quotes, no line breaks.',
        };
    }
}
