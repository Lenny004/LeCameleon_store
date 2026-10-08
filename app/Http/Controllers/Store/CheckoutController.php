<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Concerns\LoadsServiceableMunicipalities;
use App\Http\Controllers\Concerns\ResolvesStoreSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\PaymentSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    use LoadsServiceableMunicipalities;
    use ResolvesStoreSession;

    public function __construct(
        private readonly CartService $cartService,
        private readonly CheckoutService $checkoutService,
        private readonly PaymentSettingsService $paymentSettingsService,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $sessionId = $this->ensureSessionId($request, 'session_cart_key');
        $cart = $this->cartService->getCartWithItems($request->user(), $sessionId);
        $this->cartService->syncOfferPrices($cart);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        try {
            $this->checkoutService->validateCartForCheckout($cart);
        } catch (ValidationException $e) {
            return redirect()->route('cart.index')->withErrors($e->errors());
        }

        $subtotal = $this->cartService->subtotal($cart);

        return view('store.checkout.index', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'shipping' => (float) config('store.shipping_flat_rate', 0),
            'currency' => config('store.currency', 'USD'),
            'departments' => $this->serviceableDepartmentsWithMunicipalities(),
            'quoteCalculateUrl' => route('shipping.quote.calculate'),
            'transferAvailable' => $this->paymentSettingsService->transferAvailable(),
            'codEnabled' => $this->paymentSettingsService->codEnabled(),
            'codMunicipalityIds' => $this->paymentSettingsService->codMunicipalityIds(),
            'codMaxAmount' => $this->paymentSettingsService->codMaxAmount(),
            'stripeAvailable' => filled(config('services.stripe.secret')),
            'addresses' => $request->user()?->addresses()->with('municipality')->orderByDesc('is_default')->get() ?? collect(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $sessionId = $this->ensureSessionId($request, 'session_cart_key');
        $cart = $this->cartService->getCartWithItems($request->user(), $sessionId);
        $this->cartService->syncOfferPrices($cart);

        $email = $request->validated('email') ?? $request->user()?->email ?? '';

        $paymentMethod = $request->validated('payment_method') ?? 'transfer';

        $destinationMunicipalityId = $request->filled('destination_municipality_id')
            ? (int) $request->validated('destination_municipality_id')
            : null;

        $order = $this->checkoutService->placeOrder(
            $cart,
            $request->user(),
            $email,
            $request->validated('billing_address'),
            $request->validated('shipping_address'),
            $request->validated('coupon_code'),
            $request->validated('notes'),
            $paymentMethod,
            $destinationMunicipalityId,
        );

        if ($request->user() && $request->boolean('save_address') && $request->user()->addresses()->count() < 10) {
            $addressData = $request->validated('shipping_address');
            $addressData['country'] = strtoupper((string) ($addressData['country'] ?? 'SV'));
            $addressData['sv_municipality_id'] = $destinationMunicipalityId;
            $addressData['is_default'] = ! $request->user()->addresses()->exists();
            $alreadySaved = $request->user()->addresses()
                ->where('line1', $addressData['line1'])
                ->where('city', $addressData['city'])
                ->where('sv_municipality_id', $addressData['sv_municipality_id'])
                ->exists();

            if (! $alreadySaved) {
                $request->user()->addresses()->create($addressData);
            }
        }

        $request->session()->put('last_order_id', $order->id);

        $redirect = redirect()
            ->route('checkout.success', $order)
            ->with('success', 'Pedido realizado con éxito.');

        if ($paymentMethod === 'stripe' && $order->payments()->latest()->first()?->provider === 'manual') {
            $redirect->with(
                'info',
                'Stripe aún no está configurado; tu pedido se completará con pago manual.',
            );
        }

        if ($order->coupon_code) {
            $redirect->with(
                'info',
                sprintf(
                    'Cupón %s aplicado. Ahorraste $%s.',
                    $order->coupon_code,
                    number_format((float) $order->discount_total, 2),
                ),
            );
        }

        return $redirect;
    }

    public function success(Request $request, Order $order): View
    {
        $ownsOrder = $request->user() && $order->user_id === $request->user()->id;
        $sessionOrder = $request->session()->get('last_order_id') === $order->id;

        abort_unless($ownsOrder || $sessionOrder, 403);

        $order->load(['items', 'payments', 'paymentReceipts']);
        $receiptUploadUrl = $ownsOrder
            ? route('account.orders.receipts.store', $order)
            : URL::temporarySignedRoute('checkout.receipts.store', now()->addDays(7), ['order' => $order]);

        return view('store.checkout.success', [
            'order' => $order,
            'paymentInstructions' => $order->paymentInstructions(),
            'receiptUploadUrl' => $receiptUploadUrl,
        ]);
    }
}
