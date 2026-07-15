<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchApiController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalogService,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $filters = $request->only([
            'q', 'category', 'brand', 'era_decade', 'condition_grade',
            'size_label', 'color', 'price_min', 'price_max', 'in_stock', 'sort',
        ]);

        $products = $this->catalogService->paginatePublished(
            $filters,
            (int) $request->input('per_page', 12),
        );

        $suggestions = $this->catalogService
            ->searchSuggestions((string) $request->input('q', ''))
            ->map(fn ($product) => [
                'slug' => $product->slug,
                'name' => $product->name,
                'price' => $product->price,
            ])
            ->values();

        return response()->json([
            ...$products->toArray(),
            'suggestions' => $suggestions,
        ]);
    }
}
