<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'source_url' => $this->source_url,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'generations' => ContentGenerationResource::collection(
                $this->whenLoaded('generations')
            ),
        ];
    }
}
