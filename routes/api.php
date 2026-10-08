<?php

use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\SearchApiController;
use App\Http\Controllers\Api\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {
    Route::get('search', SearchApiController::class)->name('search');
    Route::get('cart', [CartApiController::class, 'show'])->name('cart.show');
    Route::post('cart/items', [CartApiController::class, 'store'])->name('cart.items.store');
    Route::post('webhooks/stripe', StripeWebhookController::class)->name('webhooks.stripe');
});
