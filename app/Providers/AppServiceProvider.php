<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\CartService;
use Illuminate\Support\Facades\Schema;
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
        View::composer(['layouts.store', 'layouts.admin'], function ($view): void {
            $storeSettings = [
                'storeName' => config('app.name', 'Le Cameleon'),
                'storeContact' => [],
            ];

            if (Schema::hasTable('settings')) {
                $settings = Setting::query()
                    ->whereIn('key', ['store.name', 'store.contact'])
                    ->get()
                    ->keyBy('key');

                $storeSettings = [
                    'storeName' => data_get($settings->get('store.name')?->value, 'en', $storeSettings['storeName']),
                    'storeContact' => $settings->get('store.contact')?->value ?? [],
                ];
            }

            $view->with($storeSettings);
        });

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
