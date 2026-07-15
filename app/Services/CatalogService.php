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

        if (! empty($filters['q'])) {
            $term = '%'.strtolower((string) $filters['q']).'%';
            $query->where(function (Builder $builder) use ($term) {
                $builder
                    ->whereRaw('LOWER(name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(sku) LIKE ?', [$term]);
            });
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

        return $this->applySort($query, (string) ($filters['sort'] ?? 'newest'));
    }

    public function findPublishedBySlug(string $slug): ?Product
    {
        return Product::query()
            ->with(['brand', 'category', 'images', 'attributes', 'reviews' => fn ($q) => $q->where('is_approved', true)])
            ->where('slug', $slug)
            ->where('status', ProductStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->first();
    }

    public function featured(int $limit = 8): Collection
    {
        return Product::query()
            ->with(['brand', 'category', 'images'])
            ->where('status', ProductStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    public function newArrivals(int $limit = 8): Collection
    {
        return $this->featured($limit);
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

    private function applySort(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('published_at'),
        };
    }
}
