<?php

namespace App\Http\Controllers\Store;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\ReviewRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;

/**
 * Storefront review submission (one review per customer per product).
 */
class ReviewController extends Controller
{
    public function store(ReviewRequest $request, Product $product): RedirectResponse
    {
        abort_unless(
            $product->status === ProductStatus::Published && $product->published_at,
            404,
        );

        // Block duplicate active reviews from the same customer.
        $alreadyReviewed = $product->reviews()
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'Ya enviaste una reseña para este producto.');
        }

        // New reviews stay hidden until an admin approves them.
        $product->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->validated('rating'),
            'title' => $request->validated('title'),
            'body' => $request->validated('body'),
            'is_approved' => false,
        ]);

        return back()->with(
            'success',
            'Gracias. Tu reseña aparecerá cuando sea aprobada.',
        );
    }
}
