<?php

use App\Http\Controllers\Api\GenerationController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('products', ProductController::class)->except('update');

    Route::post('products/{product}/generations', [GenerationController::class, 'store'])
        ->name('products.generations.store');

    Route::get('generations/{generation}', [GenerationController::class, 'show'])
        ->name('generations.show');
});
