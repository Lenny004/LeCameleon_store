<?php

namespace App\Services;

use App\Mail\StockAvailable;
use App\Models\Product;
use App\Models\StockAlert;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Waitlist subscriptions and back-in-stock notifications.
 */
class StockAlertService
{
    /**
     * Subscribe an email to restock alerts for a product.
     */
    public function subscribe(Product $product, string $email, ?User $user = null): StockAlert
    {
        return StockAlert::query()->updateOrCreate(
            [
                'product_id' => $product->id,
                'email' => $email,
            ],
            [
                'user_id' => $user?->id,
                'notified_at' => null,
            ],
        );
    }

    /**
     * Notify pending alerts when sellable stock becomes available.
     */
    public function notifyPendingAlerts(Product $product): void
    {
        $sellable = $product->quantity_available - $product->quantity_reserved;

        if ($sellable <= 0) {
            return;
        }

        $alerts = StockAlert::query()
            ->where('product_id', $product->id)
            ->whereNull('notified_at')
            ->get();

        foreach ($alerts as $alert) {
            Mail::to($alert->email)->send(new StockAvailable($product));
            $alert->update(['notified_at' => now()]);
        }
    }
}
