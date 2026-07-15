<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Approved product reviews: summaries, filtered comment lists, and catalog rating aggregates.
 */
class ReviewService
{
    /**
     * Build PDP rating summary: average score, total count, and 5→1 star histogram.
     *
     * @return array{count: int, average: float|null, distribution: array<int, int>}
     */
    public function summaryForProduct(Product $product): array
    {
        // Only moderated (approved) reviews contribute to public scores.
        $rows = Review::query()
            ->where('product_id', $product->id)
            ->where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        $count = (int) $rows->sum();
        $weighted = 0;

        foreach ($rows as $rating => $total) {
            $weighted += ((int) $rating) * ((int) $total);
        }

        // Always return all five buckets so the UI can render empty bars.
        $distribution = [];
        for ($stars = 5; $stars >= 1; $stars--) {
            $distribution[$stars] = (int) ($rows[$stars] ?? 0);
        }

        return [
            'count' => $count,
            'average' => $count > 0 ? round($weighted / $count, 1) : null,
            'distribution' => $distribution,
        ];
    }

    /**
     * Approved comments for a product, optionally filtered by star rating and sort order.
     * Query keys from the storefront: review_rating, review_sort.
     *
     * @param  array{rating?: int|string|null, sort?: string|null}  $filters
     */
    public function approvedForProduct(Product $product, array $filters = []): Collection
    {
        $query = Review::query()
            ->with('user')
            ->where('product_id', $product->id)
            ->where('is_approved', true);

        $rating = isset($filters['rating']) && $filters['rating'] !== ''
            ? (int) $filters['rating']
            : null;

        if ($rating !== null && $rating >= 1 && $rating <= 5) {
            $query->where('rating', $rating);
        }

        return $this->applySort($query, (string) ($filters['sort'] ?? 'newest'))->get();
    }

    /**
     * Attach approved_reviews_avg / approved_reviews_count for product cards and sorting.
     */
    public function applyApprovedAggregates(Builder $query): Builder
    {
        return $query
            ->withAvg(['reviews as approved_reviews_avg' => fn (Builder $q) => $q->where('is_approved', true)], 'rating')
            ->withCount(['reviews as approved_reviews_count' => fn (Builder $q) => $q->where('is_approved', true)]);
    }

    /** Sort comment lists: newest (default), oldest, highest, or lowest rating. */
    private function applySort(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            'highest' => $query->orderByDesc('rating')->orderByDesc('created_at'),
            'lowest' => $query->orderBy('rating')->orderByDesc('created_at'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
    }
}
