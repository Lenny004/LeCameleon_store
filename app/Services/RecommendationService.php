<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Enums\RelationType;
use App\Models\Product;
use App\Models\ProductRelation;
use App\Models\ProductView;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Product recommendations via explicit relations, co-views, and catalog fallbacks.
 */
class RecommendationService
{
    /**
     * Explicit relations first, then same category/brand fallback.
     */
    public function forProduct(Product $product, int $limit = 8): Collection
    {
        $limit = $limit ?: (int) config('store.recommendations_limit', 8);

        $relatedIds = ProductRelation::query()
            ->where('product_id', $product->id)
            ->orderByRaw('CASE type WHEN ? THEN 1 WHEN ? THEN 2 WHEN ? THEN 3 ELSE 4 END', [
                RelationType::Similar->value,
                RelationType::Related->value,
                RelationType::CrossSell->value,
            ])
            ->pluck('related_product_id');

        $products = $this->hydrateOrdered($relatedIds, $limit);

        if ($products->count() >= $limit) {
            return $products->take($limit);
        }

        $exclude = $products->pluck('id')->push($product->id);
        $fallback = $this->categoryBrandFallback($product, $limit - $products->count(), $exclude);

        return $products->merge($fallback)->take($limit)->values();
    }

    /**
     * Products frequently viewed in the same sessions as this product.
     */
    public function customersAlsoViewed(Product $product, int $limit = 8, ?Collection $excludeIds = null): Collection
    {
        $exclude = ($excludeIds ?? collect())->push($product->id)->unique()->values();

        $sessionIds = ProductView::query()
            ->where('product_id', $product->id)
            ->whereNotNull('session_id')
            ->where('viewed_at', '>=', now()->subDays(90))
            ->distinct()
            ->limit(500)
            ->pluck('session_id');

        if ($sessionIds->isEmpty()) {
            return collect();
        }

        $rankedIds = ProductView::query()
            ->select('product_id', DB::raw('COUNT(*) as affinity'))
            ->whereIn('session_id', $sessionIds)
            ->whereNotIn('product_id', $exclude)
            ->where('viewed_at', '>=', now()->subDays(90))
            ->groupBy('product_id')
            ->orderByDesc('affinity')
            ->limit($limit)
            ->pluck('product_id');

        return $this->hydrateOrdered($rankedIds, $limit);
    }

    public function featuredForHome(int $limit = 8): Collection
    {
        return $this->publishedQuery()
            ->with(['brand', 'category', 'images'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    private function categoryBrandFallback(Product $product, int $limit, Collection $exclude): Collection
    {
        if ($limit <= 0) {
            return collect();
        }

        return $this->publishedQuery()
            ->with(['brand', 'category', 'images'])
            ->whereNotIn('id', $exclude)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id);

                if ($product->brand_id) {
                    $query->orWhere('brand_id', $product->brand_id);
                }
            })
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    private function hydrateOrdered(Collection $ids, int $limit): Collection
    {
        if ($ids->isEmpty() || $limit <= 0) {
            return collect();
        }

        return $this->publishedQuery()
            ->whereIn('id', $ids)
            ->with(['brand', 'category', 'images'])
            ->limit($limit)
            ->get()
            ->sortBy(fn (Product $p) => $ids->search($p->id))
            ->values()
            ->take($limit);
    }

    private function publishedQuery()
    {
        return Product::query()
            ->where('status', ProductStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereColumn('quantity_available', '>', 'quantity_reserved');
    }
}
