<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Manual-first payment capture with optional Stripe webhook handling.
 *
 * Webhook signature verification is performed in StripeWebhookController.
 * Production deployments must verify Stripe-Signature before trusting payloads.
 */
class PaymentService
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    public function createPendingPayment(Order $order, string $provider = 'manual'): Payment
    {
        return Payment::query()->create([
            'order_id' => $order->id,
            'provider' => $provider,
            'status' => PaymentStatus::Pending,
            'amount' => $order->grand_total,
        ]);
    }

    public function capturePayment(Order $order, ?User $actor = null): Order
    {
        if ($order->status !== OrderStatus::Pending) {
            throw ValidationException::withMessages([
                'payment' => 'Only pending orders can have payments captured.',
            ]);
        }

        $payment = $order->payments()
            ->where('status', PaymentStatus::Pending)
            ->latest()
            ->first();

        if (! $payment) {
            throw ValidationException::withMessages([
                'payment' => 'No pending payment found for this order.',
            ]);
        }

        return DB::transaction(function () use ($order, $payment, $actor) {
            $payment->update([
                'status' => PaymentStatus::Captured,
                'transaction_reference' => $payment->transaction_reference ?? 'manual-'.now()->timestamp,
            ]);

            return $this->orderService->transition($order->fresh(), OrderStatus::Paid, $actor);
        });
    }

    /**
     * Record a manual refund when an order had a captured payment.
     */
    public function recordRefund(Order $order, float $amount, string $note): ?Payment
    {
        $hasCaptured = $order->payments()
            ->where('status', PaymentStatus::Captured)
            ->exists();

        if (! $hasCaptured) {
            return null;
        }

        return Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'manual',
            'status' => PaymentStatus::Refunded,
            'amount' => $amount,
            'transaction_reference' => 'refund-'.now()->timestamp,
            'payload' => ['note' => $note],
        ]);
    }

    public function markFailed(Order $order, ?string $reason = null): Payment
    {
        $payment = $order->payments()
            ->where('status', PaymentStatus::Pending)
            ->latest()
            ->first();

        if (! $payment) {
            throw ValidationException::withMessages([
                'payment' => 'No pending payment found for this order.',
            ]);
        }

        $payload = $payment->payload ?? [];

        if ($reason !== null) {
            $payload['failure_reason'] = $reason;
        }

        $payment->update([
            'status' => PaymentStatus::Failed,
            'payload' => $payload,
        ]);

        return $payment->fresh();
    }

    /**
     * Process a decoded Stripe webhook event (no stripe-php SDK required).
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleStripeWebhookPayload(array $payload): void
    {
        $eventType = $payload['type'] ?? null;
        $object = $payload['data']['object'] ?? [];

        if (! is_string($eventType) || ! is_array($object)) {
            return;
        }

        if (in_array($eventType, ['payment_intent.succeeded', 'checkout.session.completed'], true)) {
            $payment = $this->resolvePaymentFromStripeObject($object);

            if ($payment && $payment->order) {
                $this->capturePayment($payment->order);
            }

            return;
        }

        if ($eventType === 'payment_intent.payment_failed') {
            $payment = $this->resolvePaymentFromStripeObject($object);

            if ($payment && $payment->order) {
                $reason = $object['last_payment_error']['message'] ?? 'Payment failed';
                $this->markFailed($payment->order, is_string($reason) ? $reason : 'Payment failed');
            }
        }
    }

    /**
     * @param  array<string, mixed>  $object
     */
    private function resolvePaymentFromStripeObject(array $object): ?Payment
    {
        $reference = $object['id'] ?? null;

        if (is_string($reference) && $reference !== '') {
            $byReference = Payment::query()
                ->where('transaction_reference', $reference)
                ->where('status', PaymentStatus::Pending)
                ->latest()
                ->first();

            if ($byReference) {
                return $byReference;
            }
        }

        $paymentIntentId = $object['payment_intent'] ?? null;

        if (is_string($paymentIntentId) && $paymentIntentId !== '') {
            $byIntent = Payment::query()
                ->where('transaction_reference', $paymentIntentId)
                ->where('status', PaymentStatus::Pending)
                ->latest()
                ->first();

            if ($byIntent) {
                return $byIntent;
            }
        }

        $metadata = $object['metadata'] ?? [];
        $orderId = is_array($metadata) ? ($metadata['order_id'] ?? null) : null;
        $orderNumber = is_array($metadata) ? ($metadata['order_number'] ?? null) : null;

        $order = null;

        if (is_string($orderId) && $orderId !== '') {
            $order = Order::query()->find($orderId);
        }

        if (! $order && is_string($orderNumber) && $orderNumber !== '') {
            $order = Order::query()->where('number', $orderNumber)->first();
        }

        if (! $order) {
            return null;
        }

        return $order->payments()
            ->where('status', PaymentStatus::Pending)
            ->latest()
            ->first();
    }
}
