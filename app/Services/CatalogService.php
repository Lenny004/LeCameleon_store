<?php

namespace App\Services;

use App\Enums\ConditionGrade;
use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Published catalog queries with storefront filters and sorting.
 */
class CatalogService
{
    public function __construct(
        private readonly ReviewService $reviewService,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginatePublished(array $filters = [], int $perPage = 24): LengthAwarePaginator
    {
        return $this->buildPublishedQuery($filters)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function buildPublishedQuery(array $filters = []): Builder
    {
        $query = Product::query()
            ->with(['brand', 'category', 'images'])
            ->where('status', ProductStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        $this->reviewService->applyApprovedAggregates($query);

        $searchTerms = $this->parseSearchTerms((string) ($filters['q'] ?? ''));
        if ($searchTerms !== []) {
            $this->applySearchTerms($query, $searchTerms);
        }

        if (! empty($filters['category'])) {
            $category = Category::query()
                ->where('slug', $filters['category'])
                ->orWhere('id', $filters['category'])
                ->first();

            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if (! empty($filters['brand'])) {
            $brand = Brand::query()
                ->where('slug', $filters['brand'])
                ->orWhere('id', $filters['brand'])
                ->first();

            if ($brand) {
                $query->where('brand_id', $brand->id);
            }
        }

        if (! empty($filters['era_decade'])) {
            $query->where('era_decade', $filters['era_decade']);
        }

        if (! empty($filters['condition_grade'])) {
            $grade = ConditionGrade::tryFrom((string) $filters['condition_grade']);
            if ($grade) {
                $query->where('condition_grade', $grade);
            }
        }

        if (! empty($filters['size_label'])) {
            $query->where('size_label', $filters['size_label']);
        }

        if (! empty($filters['color'])) {
            $query->whereRaw('LOWER(color) = ?', [strtolower((string) $filters['color'])]);
        }

        if (isset($filters['price_min']) && $filters['price_min'] !== '') {
            $query->where('price', '>=', (float) $filters['price_min']);
        }

        if (isset($filters['price_max']) && $filters['price_max'] !== '') {
            $query->where('price', '<=', (float) $filters['price_max']);
        }

        if (filter_var($filters['in_stock'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $query->whereColumn('quantity_available', '>', 'quantity_reserved');
        }

        // Keep products whose approved-review average meets the shopper's minimum.
        if (isset($filters['min_rating']) && $filters['min_rating'] !== '') {
            $minRating = (int) $filters['min_rating'];
            if ($minRating >= 1 && $minRating <= 5) {
                $query->whereIn('id', function ($sub) use ($minRating) {
                    $sub->select('product_id')
                        ->from('reviews')
                        ->where('is_approved', true)
                        ->whereNull('deleted_at')
                        ->groupBy('product_id')
                        ->havingRaw('AVG(rating) >= ?', [$minRating]);
                });
            }
        }

        return $this->applySort($query, (string) ($filters['sort'] ?? 'newest'));
    }

    public function findPublishedBySlug(string $slug): ?Product
    {
        $query = Product::query()
            ->with(['brand', 'category', 'images', 'attributes'])
            ->where('slug', $slug)
            ->where('status', ProductStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        $this->reviewService->applyApprovedAggregates($query);

        return $query->first();
    }

    public function featured(int $limit = 8): Collection
    {
        $query = Product::query()
            ->with(['brand', 'category', 'images'])
            ->where('status', ProductStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit($limit);

        $this->reviewService->applyApprovedAggregates($query);

        return $query->get();
    }

    public function newArrivals(int $limit = 8): Collection
    {
        return $this->featured($limit);
    }

    /**
     * Published products for the XML sitemap.
     */
    public function publishedForSitemap(): Collection
    {
        return Product::query()
            ->where('status', ProductStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);
    }

    /**
     * Lightweight matches for autocomplete / highlight UIs.
     */
    public function searchSuggestions(string $q, int $limit = 8): Collection
    {
        $searchTerms = $this->parseSearchTerms($q);

        if ($searchTerms === []) {
            return collect();
        }

        $query = Product::query()
            ->select(['slug', 'name', 'price'])
            ->where('status', ProductStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        $this->applySearchTerms($query, $searchTerms);

        return $query
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    public function filterOptions(): array
    {
        $base = Product::query()
            ->where('status', ProductStatus::Published)
            ->whereNotNull('published_at');

        return [
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'brands' => Brand::query()->orderBy('name')->get(),
            'era_decades' => (clone $base)->whereNotNull('era_decade')->distinct()->orderBy('era_decade')->pluck('era_decade'),
            'condition_grades' => ConditionGrade::cases(),
            'size_labels' => (clone $base)->whereNotNull('size_label')->distinct()->orderBy('size_label')->pluck('size_label'),
            'colors' => (clone $base)->whereNotNull('color')->distinct()->orderBy('color')->pluck('color'),
        ];
    }

    /**
     * Active categories ranked by published product count (homepage collections).
     */
    public function topCategoriesWithPublishedCount(int $limit = 4): Collection
    {
        $publishedProductConstraints = function (Builder $query) {
            $query->where('status', ProductStatus::Published)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        };

        return Category::query()
            ->where('is_active', true)
            ->whereHas('products', $publishedProductConstraints)
            ->withCount(['products as published_products_count' => $publishedProductConstraints])
            ->orderByDesc('published_products_count')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();
    }

    /**
     * @return list<string>
     */
    private function parseSearchTerms(string $q): array
    {
        $terms = preg_split('/\s+/u', trim($q), -1, PREG_SPLIT_NO_EMPTY);

        if ($terms === false) {
            return [];
        }

        return array_values($terms);
    }

    /**
     * Each term must match at least one searchable field (AND across terms).
     *
     * @param  list<string>  $terms
     */
    private function applySearchTerms(Builder $query, array $terms): void
    {
        foreach ($terms as $term) {
            $like = '%'.strtolower($term).'%';

            $query->where(function (Builder $builder) use ($like) {
                $builder
                    ->whereRaw('LOWER(products.name) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(products.description) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(products.sku) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(products.color) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(products.material) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(products.era_decade) LIKE ?', [$like])
                    ->orWhereHas('brand', function (Builder $brandQuery) use ($like) {
                        $brandQuery->whereRaw('LOWER(name) LIKE ?', [$like]);
                    });
            });
        }
    }

    private function applySort(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            'rating' => $query->orderByDesc('approved_reviews_avg')->orderByDesc('approved_reviews_count'),
            default => $query->orderByDesc('published_at'),
        };
    }
}
