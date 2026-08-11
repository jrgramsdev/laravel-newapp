<?php

namespace App\Http\Requests;

use App\Enums\GenerationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGenerationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(GenerationType::class)],
        ];
    }

    public function type(): GenerationType
    {
        return GenerationType::from($this->validated('type'));
    }
}
