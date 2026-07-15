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
        return view('admin.dashboard', [
            'kpis' => $this->analyticsService->dashboardKpis(),
            'recentOrders' => $this->analyticsService->recentOrders(),
            'topViewed' => $this->analyticsService->topViewedProducts(),
        ]);
    }
}
