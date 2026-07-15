<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::query()
            ->with(['product', 'user'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Review approved.');
    }

    public function reject(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => false]);

        return back()->with('success', 'Review rejected.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
