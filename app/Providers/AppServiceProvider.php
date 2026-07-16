<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.store-header', function ($view): void {
            $request = request();

            if (! $request->hasSession()) {
                $view->with('cartCount', 0);

                return;
            }

            $sessionKey = config('store.session_cart_key');
            $sessionId = $request->session()->get($sessionKey);

            if (! $request->user() && ! $sessionId) {
                $view->with('cartCount', 0);

                return;
            }

            $cart = app(CartService::class)->getCartWithItems(
                $request->user(),
                $sessionId ? (string) $sessionId : null,
            );

            $view->with('cartCount', app(CartService::class)->itemCount($cart));
        });
    }
}
