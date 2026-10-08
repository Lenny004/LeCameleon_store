<?php

use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\SearchApiController;
use App\Http\Controllers\Api\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->middleware('throttle:60,1')->group(function () {
    Route::get('search', SearchApiController::class)->name('search');
    Route::get('cart', [CartApiController::class, 'show'])->name('cart.show');
    Route::post('cart/items', [CartApiController::class, 'store'])->name('cart.items.store');
});

Route::prefix('v1')->name('api.')->middleware('throttle:120,1')->group(function () {
    Route::post('webhooks/stripe', StripeWebhookController::class)->name('webhooks.stripe');
});
