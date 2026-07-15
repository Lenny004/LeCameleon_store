<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Wishlist for authenticated users and guest sessions.
 */
class WishlistService
{
    public function resolveWishlist(?User $user, ?string $sessionId): Wishlist
    {
        if ($user) {
            return Wishlist::query()->firstOrCreate(['user_id' => $user->id]);
        }

        if (! $sessionId) {
            $sessionId = (string) Str::uuid();
        }

        return Wishlist::query()->firstOrCreate(['session_id' => $sessionId]);
    }

    public function getWithItems(?User $user, ?string $sessionId): Wishlist
    {
        return $this->resolveWishlist($user, $sessionId)
            ->load(['items.product.images', 'items.product.brand']);
    }

    public function addItem(Wishlist $wishlist, Product $product): WishlistItem
    {
        if ($product->status !== ProductStatus::Published) {
            throw ValidationException::withMessages([
                'product' => 'This product cannot be added to the wishlist.',
            ]);
        }

        return $wishlist->items()->firstOrCreate([
            'product_id' => $product->id,
        ]);
    }

    public function removeItem(WishlistItem $item): void
    {
        $item->delete();
    }

    public function toggle(Wishlist $wishlist, Product $product): bool
    {
        $existing = $wishlist->items()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        $this->addItem($wishlist, $product);

        return true;
    }

    public function mergeGuestIntoUser(Wishlist $guestWishlist, User $user): Wishlist
    {
        return DB::transaction(function () use ($guestWishlist, $user) {
            $userWishlist = $this->resolveWishlist($user, null);

            foreach ($guestWishlist->items as $item) {
                $userWishlist->items()->firstOrCreate(['product_id' => $item->product_id]);
            }

            $guestWishlist->items()->delete();
            $guestWishlist->delete();

            return $userWishlist->load(['items.product.images']);
        });
    }
}
