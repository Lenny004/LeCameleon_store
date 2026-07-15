<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        $from = $from ?? now()->subDays(30)->startOfDay();
        $to = $to ?? now()->endOfDay();

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

    /**
     * Daily revenue series for charts.
     *
     * @return array{labels: list<string>, values: list<float>}
     */
    public function revenueSeries(?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = ($from ?? now()->subDays(29))->copy()->startOfDay();
        $to = ($to ?? now())->copy()->endOfDay();

        $rows = Order::query()
            ->whereBetween('placed_at', [$from, $to])
            ->whereNotIn('status', [OrderStatus::Cancelled])
            ->selectRaw('DATE(placed_at) as day, SUM(grand_total) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        return $this->fillDailySeries($from, $to, $rows, asFloat: true);
    }

    /**
     * Daily order count series for charts.
     *
     * @return array{labels: list<string>, values: list<int>}
     */
    public function ordersSeries(?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = ($from ?? now()->subDays(29))->copy()->startOfDay();
        $to = ($to ?? now())->copy()->endOfDay();

        $rows = Order::query()
            ->whereBetween('placed_at', [$from, $to])
            ->whereNotIn('status', [OrderStatus::Cancelled])
            ->selectRaw('DATE(placed_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        return $this->fillDailySeries($from, $to, $rows, asFloat: false);
    }

    public function topViewedProducts(int $limit = 10, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $from = $from ?? now()->subDays(30)->startOfDay();
        $to = $to ?? now()->endOfDay();

        return ProductView::query()
            ->selectRaw('product_id, COUNT(*) as views_count')
            ->whereBetween('viewed_at', [$from, $to])
            ->groupBy('product_id')
            ->orderByDesc('views_count')
            ->limit($limit)
            ->with('product')
            ->get();
    }

    /**
     * Top products by units sold and revenue in the period.
     */
    public function topSellingProducts(int $limit = 10, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $from = $from ?? now()->subDays(30)->startOfDay();
        $to = $to ?? now()->endOfDay();

        return OrderItem::query()
            ->select([
                'order_items.product_id',
                'order_items.name',
                DB::raw('SUM(order_items.quantity) as sales_count'),
                DB::raw('SUM(order_items.line_total) as revenue'),
            ])
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.placed_at', [$from, $to])
            ->whereNotIn('orders.status', [OrderStatus::Cancelled])
            ->whereNotNull('order_items.product_id')
            ->groupBy('order_items.product_id', 'order_items.name')
            ->orderByDesc('sales_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Merge views + sales for the analytics table.
     */
    public function topProductsReport(int $limit = 10, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $views = $this->topViewedProducts($limit * 2, $from, $to)->keyBy('product_id');
        $sales = $this->topSellingProducts($limit * 2, $from, $to)->keyBy('product_id');

        $ids = $views->keys()->merge($sales->keys())->unique();

        return $ids->map(function (string $productId) use ($views, $sales) {
            $viewRow = $views->get($productId);
            $saleRow = $sales->get($productId);
            $product = $viewRow?->product;

            return [
                'product_id' => $productId,
                'name' => $product?->name ?? $saleRow?->name ?? 'Unknown',
                'views' => (int) ($viewRow?->views_count ?? 0),
                'sales' => (int) ($saleRow?->sales_count ?? 0),
                'revenue' => (float) ($saleRow?->revenue ?? 0),
            ];
        })
            ->sortByDesc(fn (array $row) => [$row['sales'], $row['views']])
            ->values()
            ->take($limit);
    }

    public function recentOrders(int $limit = 10): Collection
    {
        return Order::query()
            ->with('user')
            ->orderByDesc('placed_at')
            ->limit($limit)
            ->get();
    }

    public function lowStockProducts(int $limit = 10): Collection
    {
        return Product::query()
            ->with(['brand', 'images'])
            ->where('status', ProductStatus::Published)
            ->whereRaw('(quantity_available - quantity_reserved) > 0')
            ->whereRaw('(quantity_available - quantity_reserved) <= low_stock_threshold')
            ->orderByRaw('(quantity_available - quantity_reserved) asc')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  \Illuminate\Support\Collection<string, mixed>  $rows
     * @return array{labels: list<string>, values: list<float|int>}
     */
    private function fillDailySeries(Carbon $from, Carbon $to, Collection $rows, bool $asFloat): array
    {
        $labels = [];
        $values = [];

        foreach (CarbonPeriod::create($from->copy()->startOfDay(), '1 day', $to->copy()->startOfDay()) as $day) {
            $key = $day->toDateString();
            $labels[] = $day->format('M j');
            $raw = $rows->get($key, 0);
            $values[] = $asFloat ? round((float) $raw, 2) : (int) $raw;
        }

        return compact('labels', 'values');
    }
}
