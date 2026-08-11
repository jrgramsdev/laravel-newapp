<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'source_url',
        'notes',
    ];

    /**
     * @return HasMany<ContentGeneration, $this>
     */
    public function generations(): HasMany
    {
        return $this->hasMany(ContentGeneration::class)->latest();
    }
}
