<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\SavedSearch;
use App\Services\AnalyticsService;
use App\Services\CatalogService;
use App\Services\RecommendationService;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Storefront catalog listing and product detail (PDP) pages.
 */
class ShopController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalogService,
        private readonly RecommendationService $recommendationService,
        private readonly AnalyticsService $analyticsService,
        private readonly ReviewService $reviewService,
    ) {}

    public function index(Request $request): View
    {
        // Includes min_rating so shoppers can filter by average star score.
        $filters = $request->only([
            'q', 'category', 'brand', 'era_decade', 'condition_grade',
            'size_label', 'color', 'price_min', 'price_max', 'in_stock',
            'min_rating', 'sort',
        ]);

        return view('store.shop.index', [
            'products' => $this->catalogService->paginatePublished(
                $filters,
                (int) config('store.catalog_per_page', 24),
            ),
            'filters' => $filters,
            'filterOptions' => $this->catalogService->filterOptions(),
            'hasActiveFilters' => SavedSearch::filtersAreActive($filters),
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

        // PDP comment filters: ?review_rating=1-5&review_sort=newest|oldest|highest|lowest
        $reviewFilters = [
            'rating' => $request->query('review_rating'),
            'sort' => $request->query('review_sort', 'newest'),
        ];

        return view('store.shop.show', [
            'product' => $product,
            'related' => $related,
            'alsoViewed' => $alsoViewed,
            'reviews' => $this->reviewService->approvedForProduct($product, $reviewFilters),
            'reviewSummary' => $this->reviewService->summaryForProduct($product),
            'reviewFilters' => $reviewFilters,
        ]);
    }

    public function search(Request $request): View
    {
        $request->merge(['q' => $request->get('q', $request->get('query'))]);

        return $this->index($request);
    }
}
