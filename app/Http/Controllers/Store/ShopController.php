<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\CatalogService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalogService,
        private readonly RecommendationService $recommendationService,
        private readonly AnalyticsService $analyticsService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only([
            'q', 'category', 'brand', 'era_decade', 'condition_grade',
            'size_label', 'color', 'price_min', 'price_max', 'in_stock', 'sort',
        ]);

        return view('store.shop.index', [
            'products' => $this->catalogService->paginatePublished(
                $filters,
                (int) config('store.catalog_per_page', 24),
            ),
            'filters' => $filters,
            'filterOptions' => $this->catalogService->filterOptions(),
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $product = $this->catalogService->findPublishedBySlug($slug);

        abort_unless($product, 404);

        $this->analyticsService->recordProductView(
            $product,
            $request->user(),
            $request->session()->getId(),
            $request->ip(),
        );

        $related = $this->recommendationService->forProduct($product);
        $alsoViewed = $this->recommendationService->customersAlsoViewed(
            $product,
            8,
            $related->pluck('id'),
        );

        return view('store.shop.show', [
            'product' => $product,
            'related' => $related,
            'alsoViewed' => $alsoViewed,
        ]);
    }

    public function search(Request $request): View
    {
        $request->merge(['q' => $request->get('q', $request->get('query'))]);

        return $this->index($request);
    }
}
