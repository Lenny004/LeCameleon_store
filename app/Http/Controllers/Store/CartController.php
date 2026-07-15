<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Concerns\ResolvesStoreSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\CartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    use ResolvesStoreSession;

    public function __construct(
        private readonly CartService $cartService,
    ) {}

    public function index(Request $request): View
    {
        $sessionId = $this->ensureSessionId($request, 'session_cart_key');
        $cart = $this->cartService->getCartWithItems($request->user(), $sessionId);

        return view('store.cart.index', [
            'cart' => $cart,
            'subtotal' => $this->cartService->subtotal($cart),
            'itemCount' => $this->cartService->itemCount($cart),
        ]);
    }

    public function store(CartItemRequest $request): RedirectResponse
    {
        $sessionId = $this->ensureSessionId($request, 'session_cart_key');
        $cart = $this->cartService->resolveCart($request->user(), $sessionId);
        $product = Product::query()->findOrFail($request->validated('product_id'));

        $this->cartService->addItem($cart, $product, (int) $request->input('quantity', 1));

        return back()->with('success', 'Item added to cart.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:99']]);

        $this->cartService->updateItem($cartItem, (int) $request->input('quantity'));

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(CartItem $cartItem): RedirectResponse
    {
        $this->cartService->removeItem($cartItem);

        return back()->with('success', 'Item removed from cart.');
    }
}
