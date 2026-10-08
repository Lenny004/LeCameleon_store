<?php

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Release stock reserved by unpaid pending orders past the configured TTL.
 */
class ReleaseExpiredReservations implements ShouldQueue
{
    use Queueable;

    public function handle(OrderService $orderService): void
    {
        $staleOrders = Order::query()
            ->where('status', OrderStatus::Pending)
            ->whereHas('payments', fn ($query) => $query->where('status', 'pending'))
            ->with(['payments' => fn ($query) => $query->where('status', 'pending')->latest()])
            ->get()
            ->filter(fn (Order $order): bool => ($expiresAt = $order->reservationExpiresAt()) !== null && $expiresAt->isPast());

        foreach ($staleOrders as $order) {
            try {
                // Cancelling releases reserved stock via OrderService.
                $orderService->cancel($order);
                Log::info("Released expired reservation for order {$order->number}.");
            } catch (\Throwable $exception) {
                Log::warning("Could not release reservation for order {$order->number}: {$exception->getMessage()}");
            }
        }
    }
}
