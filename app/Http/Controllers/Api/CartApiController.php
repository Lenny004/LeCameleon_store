<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesStoreSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\CartItemRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    use ResolvesStoreSession;

    public function __construct(
        private readonly CartService $cartService,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $sessionId = $this->ensureSessionId($request, 'session_cart_key');
        $cart = $this->cartService->getCartWithItems($request->user(), $sessionId);

        return response()->json([
            'cart' => $cart,
            'subtotal' => $this->cartService->subtotal($cart),
            'item_count' => $this->cartService->itemCount($cart),
        ]);
    }

    public function store(CartItemRequest $request): JsonResponse
    {
        $sessionId = $this->ensureSessionId($request, 'session_cart_key');
        $cart = $this->cartService->resolveCart($request->user(), $sessionId);
        $product = Product::query()->findOrFail($request->validated('product_id'));

        $item = $this->cartService->addItem($cart, $product, (int) $request->input('quantity', 1));

        return response()->json(['item' => $item->load('product')], 201);
    }
}
