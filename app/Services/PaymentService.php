<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Manual-first payment capture; Stripe keys are optional for future integration.
 */
class PaymentService
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

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
}
