<?php

namespace App\Jobs;

use App\Enums\InventoryMovementType;
use App\Enums\OrderStatus;
use App\Models\InventoryMovement;
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
        $ttlMinutes = (int) config('store.reservation_ttl_minutes', 30);
        $cutoff = now()->subMinutes($ttlMinutes);

        // Orders referenced by reserve movements older than the TTL and still pending.
        $staleOrderIds = InventoryMovement::query()
            ->where('type', InventoryMovementType::Reserve)
            ->where('reference_type', Order::class)
            ->where('created_at', '<', $cutoff)
            ->pluck('reference_id')
            ->unique()
            ->filter();

        if ($staleOrderIds->isEmpty()) {
            return;
        }

        $staleOrders = Order::query()
            ->whereIn('id', $staleOrderIds)
            ->where('status', OrderStatus::Pending)
            ->get();

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
