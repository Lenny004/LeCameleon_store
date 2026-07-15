<?php

namespace App\Services;

use App\Enums\CouponType;
use App\Enums\OrderStatus;
use App\Mail\OrderPlaced;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * Checkout flow: validate cart, reserve stock, create order, apply coupon.
 */
class CheckoutService
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly InventoryService $inventoryService,
        private readonly PaymentService $paymentService,
    ) {}

    /**
     * @param  array<string, mixed>  $billingAddress
     * @param  array<string, mixed>  $shippingAddress
     */
    public function placeOrder(
        Cart $cart,
        ?User $user,
        string $email,
        array $billingAddress,
        array $shippingAddress,
        ?string $couponCode = null,
        ?string $notes = null,
        string $paymentMethod = 'manual',
    ): Order {
        $cart->load(['items.product']);

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Your cart is empty.',
            ]);
        }

        $billingAddress['email'] = $email;
        $shippingAddress['email'] = $email;

        return DB::transaction(function () use ($cart, $user, $email, $billingAddress, $shippingAddress, $couponCode, $notes, $paymentMethod) {
            // Validate sellable stock before creating the order.
            foreach ($cart->items as $cartItem) {
                $sellableQuantity = $cartItem->product->quantity_available - $cartItem->product->quantity_reserved;

                if ($cartItem->quantity > $sellableQuantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Insufficient stock for {$cartItem->product->name}.",
                    ]);
                }
            }

            $orderSubtotal = $this->cartService->subtotal($cart);
            $appliedCoupon = $this->resolveCoupon($couponCode, $orderSubtotal);
            $discountTotal = $this->calculateDiscount($appliedCoupon, $orderSubtotal);
            $shippingTotal = (float) config('store.shipping_flat_rate', 0);
            $taxRate = (float) config('store.tax_rate', 0);
            $taxableAmount = max($orderSubtotal - $discountTotal, 0);
            $taxTotal = round($taxableAmount * $taxRate, 2);
            $grandTotal = $taxableAmount + $shippingTotal + $taxTotal;

            // Create the order first so reservations can reference it.
            $order = Order::query()->create([
                'user_id' => $user?->id,
                'number' => $this->generateOrderNumber(),
                'status' => OrderStatus::Pending,
                'currency' => config('store.currency', 'USD'),
                'subtotal' => $orderSubtotal,
                'discount_total' => $discountTotal,
                'shipping_total' => $shippingTotal,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
                'coupon_code' => $appliedCoupon?->code,
                'billing_address' => $billingAddress,
                'shipping_address' => $shippingAddress,
                'notes' => $notes,
                'placed_at' => now(),
            ]);

            foreach ($cart->items as $cartItem) {
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'name' => $cartItem->product->name,
                    'sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'line_total' => $cartItem->quantity * $cartItem->unit_price,
                    'meta' => [
                        'slug' => $cartItem->product->slug,
                        'condition_grade' => $cartItem->product->condition_grade?->value,
                    ],
                ]);

                // Hold stock against this order to prevent overselling unique pieces.
                $this->inventoryService->reserve(
                    $cartItem->product,
                    $cartItem->quantity,
                    $user,
                    'Checkout reservation for '.$order->number,
                    Order::class,
                    (string) $order->id,
                );
            }

            if ($appliedCoupon) {
                $appliedCoupon->increment('used_count');
            }

            $paymentProvider = $this->resolvePaymentProvider($paymentMethod);
            $this->paymentService->createPendingPayment($order, $paymentProvider);

            $this->cartService->clear($cart);

            $order = $order->load('items');

            if ($email) {
                Mail::to($email)->send(new OrderPlaced($order));
            }

            return $order;
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

    private function resolvePaymentProvider(string $paymentMethod): string
    {
        if ($paymentMethod === 'stripe' && empty(config('services.stripe.secret'))) {
            return 'manual';
        }

        return $paymentMethod === 'stripe' ? 'stripe' : 'manual';
    }
}
