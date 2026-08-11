<?php

namespace App\Http\Controllers\Api;

use App\Enums\GenerationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGenerationRequest;
use App\Http\Resources\ContentGenerationResource;
use App\Jobs\GenerateProductContent;
use App\Models\ContentGeneration;
use App\Models\Product;
use App\Services\Llm\PromptBuilder;
use Illuminate\Http\JsonResponse;

class GenerationController extends Controller
{
    /**
     * Queue a generation and hand back the row immediately. The provider call
     * takes seconds, which is far too long to hold an HTTP request open, so
     * the client polls `show()` until `is_complete`.
     */
    public function store(
        StoreGenerationRequest $request,
        Product $product,
        PromptBuilder $prompts,
    ): JsonResponse {
        $type = $request->type();

        $generation = $product->generations()->create([
            'type' => $type,
            'status' => GenerationStatus::Queued,
            'prompt' => $prompts->build($product, $type),
        ]);

        GenerateProductContent::dispatch($generation);

        return ContentGenerationResource::make($generation)
            ->response()
            ->setStatusCode(202);
    }

    public function show(ContentGeneration $generation): ContentGenerationResource
    {
        return ContentGenerationResource::make($generation);
    }
}
