<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use App\Services\RecommendationService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalogService,
        private readonly RecommendationService $recommendationService,
    ) {}

    public function index(): View
    {
        return view('store.home', [
            'featured' => $this->catalogService->featured(),
            'newArrivals' => $this->catalogService->newArrivals(),
            'collections' => $this->recommendationService->featuredForHome(),
            'categories' => $this->catalogService->topCategoriesWithPublishedCount(4),
        ]);
    }
}
