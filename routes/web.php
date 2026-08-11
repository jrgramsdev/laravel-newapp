<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Guest-only auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
});

// Authenticated admin area
Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// The Vue SPA. Its router owns everything under /studio, so a deep link such as
// /studio/products/3 resolves to the shell rather than 404ing on a hard refresh
// — while the Blade routes above keep their own paths.
Route::view('/studio/{any?}', 'app')
    ->where('any', '.*')
    ->name('studio');
