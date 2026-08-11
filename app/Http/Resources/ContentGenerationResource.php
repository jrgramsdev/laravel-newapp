<?php

namespace App\Http\Resources;

use App\Models\ContentGeneration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ContentGeneration
 */
class ContentGenerationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'status' => $this->status->value,
            // The frontend polls until this flips, so it never has to know
            // which status values are terminal.
            'is_complete' => $this->status->isTerminal(),
            'result' => $this->result,
            'error' => $this->error,
            'provider' => $this->provider,
            'model' => $this->model,
            'input_tokens' => $this->input_tokens,
            'output_tokens' => $this->output_tokens,
            'duration_ms' => $this->duration_ms,
            'created_at' => $this->created_at,
            'completed_at' => $this->completed_at,
        ];
    }
}
