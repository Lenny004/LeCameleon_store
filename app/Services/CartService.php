<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Shopping cart for authenticated users and guest sessions.
 */
class CartService
{
    public function resolveCart(?User $user, ?string $sessionId): Cart
    {
        if ($user) {
            return Cart::query()->firstOrCreate(['user_id' => $user->id]);
        }

        if (! $sessionId) {
            $sessionId = (string) Str::uuid();
        }

        return Cart::query()->firstOrCreate(['session_id' => $sessionId]);
    }

    public function getCartWithItems(?User $user, ?string $sessionId): Cart
    {
        $cart = $this->resolveCart($user, $sessionId);

        return $cart->load(['items.product.images', 'items.product.brand']);
    }

    public function addItem(Cart $cart, Product $product, int $quantity = 1): CartItem
    {
        $this->assertProductPurchasable($product);

        $sellable = $product->quantity_available - $product->quantity_reserved;
        $existing = $cart->items()->where('product_id', $product->id)->first();
        $newQuantity = ($existing?->quantity ?? 0) + $quantity;

        if ($newQuantity > $sellable) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$sellable} unit(s) available.",
            ]);
        }

        if ($existing) {
            $existing->update([
                'quantity' => $newQuantity,
                'unit_price' => $product->price,
            ]);

            return $existing->fresh();
        }

        return $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
        ]);
    }

    public function updateItem(CartItem $item, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            $item->delete();

            return $item;
        }

        $product = $item->product ?? Product::query()->findOrFail($item->product_id);
        $this->assertProductPurchasable($product);

        $sellable = $product->quantity_available - $product->quantity_reserved;

        if ($quantity > $sellable) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$sellable} unit(s) available.",
            ]);
        }

        $item->update([
            'quantity' => $quantity,
            'unit_price' => $product->price,
        ]);

        return $item->fresh();
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function subtotal(Cart $cart): float
    {
        return (float) $cart->items->sum(fn (CartItem $item) => $item->quantity * $item->unit_price);
    }

    public function itemCount(Cart $cart): int
    {
        return (int) $cart->items->sum('quantity');
    }

    public function mergeGuestCartIntoUser(Cart $guestCart, User $user): Cart
    {
        return DB::transaction(function () use ($guestCart, $user) {
            $userCart = $this->resolveCart($user, null);

            foreach ($guestCart->items as $guestItem) {
                $this->addItem($userCart, $guestItem->product, $guestItem->quantity);
            }

            $guestCart->items()->delete();
            $guestCart->delete();

            return $userCart->load(['items.product.images']);
        });
    }

    private function assertProductPurchasable(Product $product): void
    {
        if ($product->status !== ProductStatus::Published) {
            throw ValidationException::withMessages([
                'product' => 'This product is not available for purchase.',
            ]);
        }

        if (! $product->isInStock()) {
            throw ValidationException::withMessages([
                'product' => 'This product is out of stock.',
            ]);
        }
    }
}
