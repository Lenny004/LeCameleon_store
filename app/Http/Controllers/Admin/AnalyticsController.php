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
        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();

        return view('admin.analytics.index', [
            'kpis' => $this->analyticsService->dashboardKpis($from, $to),
            'topViewed' => $this->analyticsService->topViewedProducts(20, $from, $to),
            'from' => $from,
            'to' => $to,
        ]);
    }
}
