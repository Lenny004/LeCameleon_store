<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analyticsService,
    ) {}

    public function index(Request $request): View
    {
        $from = $request->date('from')?->startOfDay() ?? now()->subDays(29)->startOfDay();
        $to = $request->date('to')?->endOfDay() ?? now()->endOfDay();

        $kpis = $this->analyticsService->dashboardKpis($from, $to);
        $revenueSeries = $this->analyticsService->revenueSeries($from, $to);
        $ordersSeries = $this->analyticsService->ordersSeries($from, $to);

        return view('admin.analytics.index', [
            'kpis' => $kpis,
            'from' => $from,
            'to' => $to,
            'pageViews' => $kpis['product_views'],
            'cartRate' => $kpis['conversion_rate'],
            'aov' => $kpis['average_order_value'],
            'returnRate' => null,
            'salesLabels' => $revenueSeries['labels'],
            'salesValues' => $revenueSeries['values'],
            'ordersLabels' => $ordersSeries['labels'],
            'ordersValues' => $ordersSeries['values'],
            'topProducts' => $this->analyticsService->topProductsReport(15, $from, $to),
            'topViewed' => $this->analyticsService->topViewedProducts(20, $from, $to),
            'lowStockCount' => $kpis['low_stock_count'],
            'outOfStockCount' => $kpis['out_of_stock_count'],
        ]);
    }
}
