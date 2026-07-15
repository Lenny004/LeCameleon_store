<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Admin order lifecycle and stock side-effects on status changes.
 */
class OrderService
{
    /** @var array<string, list<string>> */
    private const TRANSITIONS = [
        'pending' => ['paid', 'cancelled'],
        'paid' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped' => ['delivered', 'refunded'],
        'delivered' => ['refunded'],
        'cancelled' => [],
        'refunded' => [],
    ];

    public function __construct(
        private readonly InventoryService $inventoryService,
    ) {}

    public function transition(Order $order, OrderStatus $newStatus, ?User $actor = null): Order
    {
        $current = $order->status->value;

        if (! in_array($newStatus->value, self::TRANSITIONS[$current] ?? [], true)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition from {$current} to {$newStatus->value}.",
            ]);
        }

        return DB::transaction(function () use ($order, $newStatus, $actor, $current) {
            $order->load('items.product');

            if ($newStatus === OrderStatus::Cancelled) {
                $this->releaseOrderStock($order, $actor);
            }

            if ($newStatus === OrderStatus::Paid && $current !== 'cancelled') {
                $this->fulfillOrderStock($order, $actor);
            }

            if ($newStatus === OrderStatus::Refunded) {
                $this->restockOrder($order, $actor);
            }

            $order->update(['status' => $newStatus]);

            return $order->fresh(['items', 'user']);
        });
    }

    public function cancel(Order $order, ?User $actor = null): Order
    {
        return $this->transition($order, OrderStatus::Cancelled, $actor);
    }

    private function releaseOrderStock(Order $order, ?User $actor): void
    {
        foreach ($order->items as $item) {
            if (! $item->product) {
                continue;
            }

            $this->inventoryService->release(
                $item->product,
                $item->quantity,
                $actor,
                "Order {$order->number} cancelled",
                Order::class,
                $order->id,
            );
        }
    }

    private function fulfillOrderStock(Order $order, ?User $actor): void
    {
        foreach ($order->items as $item) {
            if (! $item->product) {
                continue;
            }

            $this->inventoryService->stockOut(
                $item->product,
                $item->quantity,
                $actor,
                "Order {$order->number} paid",
                fromReservation: true,
                referenceType: Order::class,
                referenceId: $order->id,
            );
        }
    }

    private function restockOrder(Order $order, ?User $actor): void
    {
        foreach ($order->items as $item) {
            if (! $item->product) {
                continue;
            }

            $this->inventoryService->stockIn(
                $item->product,
                $item->quantity,
                $actor,
                "Order {$order->number} refunded",
            );
        }
    }
}
