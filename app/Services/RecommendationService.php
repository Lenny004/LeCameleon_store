<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Enums\RelationType;
use App\Models\Product;
use App\Models\ProductRelation;
use Illuminate\Support\Collection;

/**
 * Product recommendations via explicit relations and catalog fallbacks.
 */
class RecommendationService
{
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

        $products = $this->publishedQuery()
            ->whereIn('id', $relatedIds)
            ->with(['brand', 'category', 'images'])
            ->limit($limit)
            ->get()
            ->sortBy(fn (Product $p) => $relatedIds->search($p->id))
            ->values();

        if ($products->count() >= $limit) {
            return $products->take($limit);
        }

        $exclude = $products->pluck('id')->push($product->id);

        $fallback = $this->publishedQuery()
            ->with(['brand', 'category', 'images'])
            ->whereNotIn('id', $exclude)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id);

                if ($product->brand_id) {
                    $query->orWhere('brand_id', $product->brand_id);
                }
            })
            ->orderByDesc('published_at')
            ->limit($limit - $products->count())
            ->get();

        return $products->merge($fallback)->take($limit)->values();
    }

    public function featuredForHome(int $limit = 8): Collection
    {
        return $this->publishedQuery()
            ->with(['brand', 'category', 'images'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
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
