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
        private readonly ShippingRateService $shippingRateService,
        private readonly ShipmentTrackingService $shipmentTrackingService,
        private readonly OfferPricingService $offerPricingService,
        private readonly PaymentSettingsService $paymentSettingsService,
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
        ?int $destinationMunicipalityId = null,
        bool $sendEmail = true,
        ?float $shippingOverride = null,
    ): Order {
        $cart->load(['items.product']);

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Tu carrito está vacío.',
            ]);
        }

        if (! in_array($paymentMethod, ['transfer', 'cod', 'stripe', 'manual'], true)) {
            throw ValidationException::withMessages(['payment_method' => 'Selecciona un método de pago válido.']);
        }

        $billingAddress['email'] = $email;
        $shippingAddress['email'] = $email;

        $order = DB::transaction(function () use ($cart, $user, $email, $billingAddress, $shippingAddress, $couponCode, $notes, $paymentMethod, $destinationMunicipalityId, $shippingOverride) {
            // Validate sellable stock before creating the order.
            foreach ($cart->items as $cartItem) {
                $cartItem->unit_price = $this->offerPricingService->effectivePriceFor($user, $cartItem->product);
                $cartItem->save();
                $sellableQuantity = $cartItem->product->quantity_available - $cartItem->product->quantity_reserved;

                if ($cartItem->quantity > $sellableQuantity) {
                    throw ValidationException::withMessages([
                        'cart' => "No hay existencias suficientes de {$cartItem->product->name}.",
                    ]);
                }
            }

            $orderSubtotal = $this->cartService->subtotal($cart);
            $appliedCoupon = $this->resolveCoupon($couponCode, $orderSubtotal, $user, $email, $cart->items);

            $shippingTotal = $shippingOverride ?? $this->resolveShippingTotal($destinationMunicipalityId);
            $eligibleSubtotal = $this->eligibleSubtotal($appliedCoupon, $cart->items);
            $discountBase = $appliedCoupon?->shipping_only ? $shippingTotal : $eligibleSubtotal;
            $discountTotal = $this->calculateDiscount($appliedCoupon, $discountBase);

            if ($destinationMunicipalityId) {
                $shippingAddress['sv_municipality_id'] = $destinationMunicipalityId;
            }
            $taxRate = (float) config('store.tax_rate', 0);
            $taxableAmount = max($orderSubtotal - $discountTotal, 0);
            $taxTotal = round($taxableAmount * $taxRate, 2);
            $grandTotal = $taxableAmount + $shippingTotal + $taxTotal;

            if ($paymentMethod === 'transfer' && ! $this->paymentSettingsService->transferAvailable()) {
                throw ValidationException::withMessages(['payment_method' => 'La transferencia bancaria no está disponible en este momento.']);
            }
            if ($paymentMethod === 'cod' && ! $this->paymentSettingsService->codAvailable($destinationMunicipalityId, $grandTotal)) {
                throw ValidationException::withMessages(['payment_method' => 'El pago contra entrega no está disponible para tu zona.']);
            }

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
                $offer = $this->offerPricingService->offerFor($user, $cartItem->product);
                $orderItem = $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'name' => $cartItem->product->name,
                    'sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'line_total' => $cartItem->quantity * $cartItem->unit_price,
                    'meta' => [
                        'slug' => $cartItem->product->slug,
                        'condition_grade' => $cartItem->product->condition_grade?->value,
                        ...($offer ? ['offer_id' => $offer->id, 'original_price' => (float) $cartItem->product->price] : []),
                    ],
                ]);

                if ($offer) {
                    $offer->update(['order_id' => $order->id]);
                }

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

            $warehouseId = config('store.warehouse_municipality_id');
            $warehouseId = is_numeric($warehouseId) ? (int) $warehouseId : null;

            $this->shipmentTrackingService->createForOrder(
                $order,
                $destinationMunicipalityId,
                $warehouseId,
            );

            $order = $order->load('items');

            return $order;
        });

        if ($email && $sendEmail) {
            Mail::to($email)->queue((new OrderPlaced($order))->afterCommit());
        }

        return $order;
    }

    public function validateCartForCheckout(Cart $cart): void
    {
        $cart->load(['items.product']);

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Tu carrito está vacío.',
            ]);
        }

        foreach ($cart->items as $item) {
            $sellable = $item->product->quantity_available - $item->product->quantity_reserved;

            if ($item->quantity > $sellable) {
                throw ValidationException::withMessages([
                    'cart' => "No hay existencias suficientes de {$item->product->name}.",
                ]);
            }
        }
    }

    private function resolveCoupon(?string $code, float $subtotal, ?User $user, string $email, $items): ?Coupon
    {
        if (! $code) {
            return null;
        }

        $coupon = Coupon::query()->where('code', strtoupper(trim($code)))->first();

        if (! $coupon || ! $coupon->isValid()) {
            throw ValidationException::withMessages([
                'coupon_code' => 'El cupón no es válido o ha expirado.',
            ]);
        }

        if ($coupon->min_order_amount && $subtotal < (float) $coupon->min_order_amount) {
            throw ValidationException::withMessages([
                'coupon_code' => 'El cupón no es válido o ha expirado.',
            ]);
        }

        $customerOrders = Order::query()
            ->whereNotIn('status', [OrderStatus::Cancelled->value])
            ->where(function ($query) use ($user, $email): void {
                if ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('shipping_address->email', $email);
                } else {
                    $query->where('shipping_address->email', $email);
                }
            });

        if ($coupon->first_order_only && (clone $customerOrders)->exists()) {
            throw ValidationException::withMessages([
                'coupon_code' => 'El cupón no es válido o ha expirado.',
            ]);
        }

        if ($coupon->max_uses_per_user !== null) {
            $uses = (clone $customerOrders)->where('coupon_code', $coupon->code)->count();
            if ($uses >= $coupon->max_uses_per_user) {
                throw ValidationException::withMessages([
                    'coupon_code' => 'El cupón no es válido o ha expirado.',
                ]);
            }
        }

        if ($this->eligibleSubtotal($coupon, $items) <= 0) {
            throw ValidationException::withMessages([
                'coupon_code' => 'El cupón no es válido o ha expirado.',
            ]);
        }

        return $coupon;
    }

    private function eligibleSubtotal(?Coupon $coupon, $items): float
    {
        if (! $coupon) {
            return 0.0;
        }

        $categoryIds = $coupon->categories()->pluck('categories.id')->map(fn ($id) => (int) $id)->all();
        $productIds = $coupon->products()->pluck('products.id')->map(fn ($id) => (string) $id)->all();

        return (float) collect($items)->sum(function ($item) use ($categoryIds, $productIds): float {
            $product = $item->product;
            $categoryMatches = $categoryIds === [] || in_array((int) $product->category_id, $categoryIds, true);
            $productMatches = $productIds === [] || in_array((string) $product->id, $productIds, true);

            return $categoryMatches && $productMatches
                ? (float) $item->unit_price * (int) $item->quantity
                : 0.0;
        });
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

    private function resolveShippingTotal(?int $destinationMunicipalityId): float
    {
        if (! $destinationMunicipalityId) {
            return (float) config('store.shipping_flat_rate', 0);
        }

        $originId = config('store.warehouse_municipality_id');
        $originId = is_numeric($originId) ? (int) $originId : null;

        $quote = $this->shippingRateService->quote($originId, $destinationMunicipalityId);

        return (float) $quote['fee'];
    }

    private function resolvePaymentProvider(string $paymentMethod): string
    {
        if ($paymentMethod === 'stripe' && empty(config('services.stripe.secret'))) {
            return 'manual';
        }

        return match ($paymentMethod) {
            'transfer', 'cod', 'manual' => $paymentMethod,
            default => 'stripe',
        };
    }
}
