<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Product view tracking and admin KPI aggregates.
 */
class AnalyticsService
{
    public function recordProductView(
        Product $product,
        ?User $user = null,
        ?string $sessionId = null,
        ?string $ipAddress = null,
    ): ProductView {
        return ProductView::query()->create([
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'session_id' => $sessionId,
            'ip_address' => $ipAddress,
            'viewed_at' => now(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboardKpis(?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = $from ?? now()->subDays(30);
        $to = $to ?? now();

        $ordersQuery = Order::query()
            ->whereBetween('placed_at', [$from, $to])
            ->whereNotIn('status', [OrderStatus::Cancelled]);

        $revenue = (float) (clone $ordersQuery)->sum('grand_total');
        $orderCount = (clone $ordersQuery)->count();

        $views = ProductView::query()
            ->whereBetween('viewed_at', [$from, $to])
            ->count();

        $lowStock = Product::query()
            ->where('status', ProductStatus::Published)
            ->whereRaw('(quantity_available - quantity_reserved) > 0')
            ->whereRaw('(quantity_available - quantity_reserved) <= low_stock_threshold')
            ->count();

        $outOfStock = Product::query()
            ->where('status', ProductStatus::Published)
            ->whereColumn('quantity_available', '<=', 'quantity_reserved')
            ->count();

        return [
            'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'revenue' => $revenue,
            'orders_count' => $orderCount,
            'average_order_value' => $orderCount > 0 ? round($revenue / $orderCount, 2) : 0,
            'product_views' => $views,
            'conversion_rate' => $views > 0 ? round(($orderCount / $views) * 100, 2) : 0,
            'low_stock_count' => $lowStock,
            'out_of_stock_count' => $outOfStock,
        ];
    }

    public function topViewedProducts(int $limit = 10, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $from = $from ?? now()->subDays(30);
        $to = $to ?? now();

        return ProductView::query()
            ->selectRaw('product_id, COUNT(*) as views_count')
            ->whereBetween('viewed_at', [$from, $to])
            ->groupBy('product_id')
            ->orderByDesc('views_count')
            ->limit($limit)
            ->with('product')
            ->get();
    }

    public function recentOrders(int $limit = 10): Collection
    {
        return Order::query()
            ->with('user')
            ->orderByDesc('placed_at')
            ->limit($limit)
            ->get();
    }
}
