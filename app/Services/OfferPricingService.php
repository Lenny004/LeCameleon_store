<?php

namespace App\Services;

use App\Enums\OfferStatus;
use App\Models\Offer;
use App\Models\Product;
use App\Models\User;

class OfferPricingService
{
    public function effectivePriceFor(?User $user, Product $product): float
    {
        $normal = (float) $product->price;

        if (! $user) {
            return $normal;
        }

        $accepted = Offer::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('status', OfferStatus::Accepted)
            ->whereNull('order_id')
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('responded_at')
            ->first();

        return $accepted ? min($normal, (float) ($accepted->accepted_amount ?? $accepted->amount)) : $normal;
    }

    public function offerFor(?User $user, Product $product): ?Offer
    {
        if (! $user) {
            return null;
        }

        return Offer::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('status', OfferStatus::Accepted)
            ->whereNull('order_id')
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('responded_at')
            ->first();
    }
}
