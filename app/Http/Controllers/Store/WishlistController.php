<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Concerns\ResolvesStoreSession;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WishlistItem;
use App\Services\WishlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    use ResolvesStoreSession;

    public function __construct(
        private readonly WishlistService $wishlistService,
    ) {}

    public function index(Request $request): View
    {
        $sessionId = $this->ensureSessionId($request, 'session_wishlist_key');
        $wishlist = $this->wishlistService->getWithItems($request->user(), $sessionId);

        return view('store.wishlist.index', compact('wishlist'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['product_id' => ['required', 'uuid', 'exists:products,id']]);

        $sessionId = $this->ensureSessionId($request, 'session_wishlist_key');
        $wishlist = $this->wishlistService->resolveWishlist($request->user(), $sessionId);
        $product = Product::query()->findOrFail($request->input('product_id'));

        $this->wishlistService->addItem($wishlist, $product);

        return back()->with('success', 'Added to wishlist.');
    }

    public function destroy(Request $request, WishlistItem $wishlistItem): RedirectResponse
    {
        $this->authorizeWishlistItem($request, $wishlistItem);

        $this->wishlistService->removeItem($wishlistItem);

        return back()->with('success', 'Eliminado de favoritos.');
    }

    public function toggle(Request $request): RedirectResponse
    {
        $request->validate(['product_id' => ['required', 'uuid', 'exists:products,id']]);

        $sessionId = $this->ensureSessionId($request, 'session_wishlist_key');
        $wishlist = $this->wishlistService->resolveWishlist($request->user(), $sessionId);
        $product = Product::query()->findOrFail($request->input('product_id'));

        $added = $this->wishlistService->toggle($wishlist, $product);

        return back()->with('success', $added ? 'Added to wishlist.' : 'Removed from wishlist.');
    }

    private function authorizeWishlistItem(Request $request, WishlistItem $wishlistItem): void
    {
        $sessionId = $this->ensureSessionId($request, 'session_wishlist_key');
        $wishlist = $this->wishlistService->resolveWishlist($request->user(), $sessionId);

        abort_unless($wishlistItem->wishlist_id === $wishlist->id, 403);
    }
}
