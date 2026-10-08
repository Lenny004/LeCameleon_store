<?php

namespace App\Providers;

use App\Mail\WelcomeMail;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\WhatsAppLinkService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
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
        Event::listen(Verified::class, function (Verified $event): void {
            if ($event->user?->email) {
                Mail::to($event->user->email)->queue((new WelcomeMail($event->user))->afterCommit());
            }
        });
        View::composer(['layouts.store', 'layouts.admin'], function ($view): void {
            $storeSettings = [
                'storeName' => config('app.name', 'Le Cameleon'),
                'storeContact' => [],
                'whatsapp' => null,
            ];

            if (Schema::hasTable('settings')) {
                $settings = Setting::query()
                    ->whereIn('key', ['store.name', 'store.contact'])
                    ->get()
                    ->keyBy('key');

                $storeSettings = [
                    'storeName' => data_get($settings->get('store.name')?->value, 'en', $storeSettings['storeName']),
                    'storeContact' => $settings->get('store.contact')?->value ?? [],
                    'whatsapp' => app(WhatsAppLinkService::class)->link(),
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
