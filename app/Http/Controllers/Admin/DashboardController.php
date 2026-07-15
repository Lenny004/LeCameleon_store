<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analyticsService,
    ) {}

    public function index(): View
    {
        $from = now()->subDays(29)->startOfDay();
        $to = now()->endOfDay();
        $kpis = $this->analyticsService->dashboardKpis($from, $to);
        $revenueSeries = $this->analyticsService->revenueSeries($from, $to);
        $ordersSeries = $this->analyticsService->ordersSeries($from, $to);

        return view('admin.dashboard', [
            'kpis' => $kpis,
            'revenue' => $kpis['revenue'],
            'ordersCount' => $kpis['orders_count'],
            'lowStockCount' => $kpis['low_stock_count'],
            'conversion' => $kpis['conversion_rate'],
            'salesLabels' => $revenueSeries['labels'],
            'salesValues' => $revenueSeries['values'],
            'ordersLabels' => $ordersSeries['labels'],
            'ordersValues' => $ordersSeries['values'],
            'recentOrders' => $this->analyticsService->recentOrders(),
            'topViewed' => $this->analyticsService->topViewedProducts(),
            'lowStockProducts' => $this->analyticsService->lowStockProducts(5),
        ]);
    }
}
