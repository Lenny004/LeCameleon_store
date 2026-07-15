<?php

namespace App\Services;

use App\Enums\CouponType;
use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Checkout flow: validate cart, reserve stock, create order, apply coupon.
 */
class CheckoutService
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly InventoryService $inventoryService,
    ) {}

    /**
     * @param  array<string, mixed>  $billingAddress
     * @param  array<string, mixed>  $shippingAddress
     */
    public function placeOrder(
        Cart $cart,
        User $user,
        array $billingAddress,
        array $shippingAddress,
        ?string $couponCode = null,
        ?string $notes = null,
    ): Order {
        $cart->load(['items.product']);

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Your cart is empty.',
            ]);
        }

        return DB::transaction(function () use ($cart, $user, $billingAddress, $shippingAddress, $couponCode, $notes) {
            foreach ($cart->items as $item) {
                $sellable = $item->product->quantity_available - $item->product->quantity_reserved;

                if ($item->quantity > $sellable) {
                    throw ValidationException::withMessages([
                        'cart' => "Insufficient stock for {$item->product->name}.",
                    ]);
                }

                $this->inventoryService->reserve(
                    $item->product,
                    $item->quantity,
                    $user,
                    'Checkout reservation',
                    Order::class,
                    null,
                );
            }

            $subtotal = $this->cartService->subtotal($cart);
            $coupon = $this->resolveCoupon($couponCode, $subtotal);
            $discount = $this->calculateDiscount($coupon, $subtotal);
            $shipping = (float) config('store.shipping_flat_rate', 0);
            $taxRate = (float) config('store.tax_rate', 0);
            $taxable = max($subtotal - $discount, 0);
            $tax = round($taxable * $taxRate, 2);
            $grandTotal = max($subtotal - $discount, 0) + $shipping + $tax;

            $order = Order::query()->create([
                'user_id' => $user->id,
                'number' => $this->generateOrderNumber(),
                'status' => OrderStatus::Pending,
                'currency' => config('store.currency', 'USD'),
                'subtotal' => $subtotal,
                'discount_total' => $discount,
                'shipping_total' => $shipping,
                'tax_total' => $tax,
                'grand_total' => $grandTotal,
                'coupon_code' => $coupon?->code,
                'billing_address' => $billingAddress,
                'shipping_address' => $shippingAddress,
                'notes' => $notes,
                'placed_at' => now(),
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->quantity * $item->unit_price,
                    'meta' => [
                        'slug' => $item->product->slug,
                        'condition_grade' => $item->product->condition_grade?->value,
                    ],
                ]);
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            $this->cartService->clear($cart);

            return $order->load('items');
        });
    }

    public function validateCartForCheckout(Cart $cart): void
    {
        $cart->load(['items.product']);

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Your cart is empty.',
            ]);
        }

        foreach ($cart->items as $item) {
            $sellable = $item->product->quantity_available - $item->product->quantity_reserved;

            if ($item->quantity > $sellable) {
                throw ValidationException::withMessages([
                    'cart' => "Insufficient stock for {$item->product->name}.",
                ]);
            }
        }
    }

    private function resolveCoupon(?string $code, float $subtotal): ?Coupon
    {
        if (! $code) {
            return null;
        }

        $coupon = Coupon::query()->where('code', strtoupper(trim($code)))->first();

        if (! $coupon || ! $coupon->isValid()) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Invalid or expired coupon.',
            ]);
        }

        if ($coupon->min_order_amount && $subtotal < (float) $coupon->min_order_amount) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Order does not meet the minimum amount for this coupon.',
            ]);
        }

        return $coupon;
    }

    private function calculateDiscount(?Coupon $coupon, float $subtotal): float
    {
        if (! $coupon) {
            return 0.0;
        }

        return match ($coupon->type) {
            CouponType::Percent => round($subtotal * ((float) $coupon->value / 100), 2),
            CouponType::Fixed => min((float) $coupon->value, $subtotal),
        };
    }

    private function generateOrderNumber(): string
    {
        $prefix = config('store.order_number_prefix', 'LC');

        return sprintf('%s-%s-%s', $prefix, now()->format('Ymd'), strtoupper(substr(uniqid(), -6)));
    }
}
