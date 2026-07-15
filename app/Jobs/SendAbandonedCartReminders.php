<?php

namespace App\Jobs;

use App\Mail\AbandonedCart;
use App\Models\Cart;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Email registered users whose carts have been idle past the configured threshold.
 */
class SendAbandonedCartReminders implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $hours = (int) config('store.abandoned_cart_hours', 24);
        $cutoff = now()->subHours($hours);

        $carts = Cart::query()
            ->whereNull('reminded_at')
            ->whereNotNull('user_id')
            ->whereHas('items', fn ($query) => $query->where('updated_at', '<', $cutoff))
            ->whereDoesntHave('items', fn ($query) => $query->where('updated_at', '>=', $cutoff))
            ->with(['items.product', 'user'])
            ->get();

        foreach ($carts as $cart) {
            $email = $cart->user?->email;

            if (! $email) {
                continue;
            }

            try {
                Mail::to($email)->send(new AbandonedCart($cart));
                $cart->update(['reminded_at' => now()]);
            } catch (\Throwable $exception) {
                Log::warning("Abandoned cart reminder failed for cart {$cart->id}: {$exception->getMessage()}");
            }
        }
    }
}
