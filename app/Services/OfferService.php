<?php

namespace App\Services;

use App\Enums\OfferStatus;
use App\Enums\ProductStatus;
use App\Mail\OfferReceived;
use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * Buyer offer submission and staff response workflow.
 */
class OfferService
{
    /**
     * Create a pending offer for a published in-stock product.
     */
    public function submit(Product $product, User $user, float $amount, ?string $message = null): Offer
    {
        $this->ensureOfferable($product);

        $offer = Offer::query()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'amount' => $amount,
            'message' => $message,
            'status' => OfferStatus::Pending,
        ]);

        $offer->load(['product', 'user']);

        Mail::to(config('mail.from.address'))->send(new OfferReceived($offer));

        return $offer;
    }

    /**
     * @throws ValidationException
     */
    public function ensureOfferable(Product $product): void
    {
        if ($product->status !== ProductStatus::Published) {
            throw ValidationException::withMessages([
                'product' => 'Este producto no acepta ofertas en este momento.',
            ]);
        }

        if ($product->published_at === null || $product->published_at->isFuture()) {
            throw ValidationException::withMessages([
                'product' => 'Este producto no acepta ofertas en este momento.',
            ]);
        }

        if (! $product->isInStock()) {
            throw ValidationException::withMessages([
                'product' => 'No puedes hacer una oferta en un producto agotado.',
            ]);
        }
    }
}
