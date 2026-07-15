<?php

namespace App\Http\Controllers\Store;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\ReviewRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request, Product $product): RedirectResponse
    {
        abort_unless(
            $product->status === ProductStatus::Published && $product->published_at,
            404,
        );

        $product->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->validated('rating'),
            'title' => $request->validated('title'),
            'body' => $request->validated('body'),
            'is_approved' => false,
        ]);

        return back()->with(
            'success',
            'Thank you. Your review will appear after moderation.',
        );
    }
}
