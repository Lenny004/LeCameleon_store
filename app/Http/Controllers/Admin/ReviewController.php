<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin moderation queue for product ratings and comments.
 */
class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        // Optional filters: status (approved|pending), star rating, free-text search.
        $filters = $request->only(['status', 'rating', 'q']);

        $reviews = Review::query()
            ->with(['product', 'user'])
            ->when(
                ($filters['status'] ?? '') === 'approved',
                fn ($q) => $q->approved(),
            )
            ->when(
                ($filters['status'] ?? '') === 'pending',
                fn ($q) => $q->pending(),
            )
            ->when(
                isset($filters['rating']) && $filters['rating'] !== '',
                function ($q) use ($filters) {
                    $rating = (int) $filters['rating'];
                    if ($rating >= 1 && $rating <= 5) {
                        $q->where('rating', $rating);
                    }
                },
            )
            ->when(
                filled($filters['q'] ?? null),
                function ($q) use ($filters) {
                    $term = '%'.mb_strtolower(trim((string) $filters['q'])).'%';
                    $q->where(function ($builder) use ($term) {
                        $builder
                            ->whereRaw('LOWER(title) LIKE ?', [$term])
                            ->orWhereRaw('LOWER(body) LIKE ?', [$term])
                            ->orWhereHas('product', fn ($p) => $p->whereRaw('LOWER(name) LIKE ?', [$term]))
                            ->orWhereHas('user', fn ($u) => $u->whereRaw('LOWER(name) LIKE ?', [$term])
                                ->orWhereRaw('LOWER(email) LIKE ?', [$term]));
                    });
                },
            )
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'filters' => $filters,
        ]);
    }

    /** Publish a review on the storefront PDP. */
    public function approve(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Reseña aprobada.');
    }

    /** Hide a previously approved review from the storefront. */
    public function reject(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => false]);

        return back()->with('success', 'Reseña rechazada.');
    }

    /** Soft-delete a review (removed from moderation list and PDP). */
    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Reseña eliminada.');
    }
}
