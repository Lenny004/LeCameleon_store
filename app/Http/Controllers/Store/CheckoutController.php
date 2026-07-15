<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Concerns\ResolvesStoreSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    use ResolvesStoreSession;

    public function __construct(
        private readonly CartService $cartService,
        private readonly CheckoutService $checkoutService,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $sessionId = $this->ensureSessionId($request, 'session_cart_key');
        $cart = $this->cartService->getCartWithItems($request->user(), $sessionId);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        try {
            $this->checkoutService->validateCartForCheckout($cart);
        } catch (ValidationException $e) {
            return redirect()->route('cart.index')->withErrors($e->errors());
        }

        return view('store.checkout.index', [
            'cart' => $cart,
            'subtotal' => $this->cartService->subtotal($cart),
            'shipping' => (float) config('store.shipping_flat_rate', 0),
            'currency' => config('store.currency', 'USD'),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $sessionId = $this->ensureSessionId($request, 'session_cart_key');
        $cart = $this->cartService->getCartWithItems($request->user(), $sessionId);

        $order = $this->checkoutService->placeOrder(
            $cart,
            $request->user(),
            $request->validated('billing_address'),
            $request->validated('shipping_address'),
            $request->validated('coupon_code'),
            $request->validated('notes'),
        );

        $redirect = redirect()
            ->route('checkout.success', $order)
            ->with('success', 'Order placed successfully.');

        if ($order->coupon_code) {
            $redirect->with(
                'info',
                sprintf(
                    'Coupon %s applied. You saved $%s.',
                    $order->coupon_code,
                    number_format((float) $order->discount_total, 2),
                ),
            );
        }

        return $redirect;
    }

    public function success(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()?->id, 403);

        return view('store.checkout.success', ['order' => $order->load('items')]);
    }
}
