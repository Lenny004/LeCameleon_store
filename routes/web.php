<?php

use App\Http\Controllers\Store\AccountController;
use App\Http\Controllers\Store\AuthController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\ShopController;
use App\Http\Controllers\Store\WishlistController;
use Illuminate\Support\Facades\Route;

Route::name('store.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
    Route::get('/search', [ShopController::class, 'search'])->name('search');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{wishlistItem}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login.store');
        Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register.store');
    });

    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth')
        ->name('auth.logout');

    Route::middleware('auth')->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

        Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
        Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
        Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders.index');
        Route::get('/account/orders/{order}', [AccountController::class, 'showOrder'])->name('account.orders.show');
    });
});
