<?php

namespace App\Http\Controllers\Store;

use App\Enums\OfferStatus;
use App\Enums\ReturnRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\PasswordUpdateRequest;
use App\Http\Requests\Store\ProfileUpdateRequest;
use App\Http\Requests\Store\ReturnRequestFormRequest;
use App\Mail\OfferBuyerResponded;
use App\Models\Offer;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function profile(Request $request): View
    {
        return view('store.account.profile', [
            'user' => $request->user()->load('addresses'),
        ]);
    }

    public function updateProfile(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $emailChanged = $user->email !== $data['email'];

        if ($emailChanged) {
            $data['email_verified_at'] = null;
        }

        $user->forceFill($data)->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return back()->with(
            'success',
            $emailChanged
                ? 'Perfil actualizado. Te enviamos un correo para verificar tu nueva dirección.'
                : 'Perfil actualizado.',
        );
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
            'remember_token' => Str::random(60),
        ]);

        return back()->with('success', 'Contraseña actualizada.');
    }

    public function orders(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->withCount('items')
            ->orderByDesc('placed_at')
            ->paginate(15);

        return view('store.account.orders.index', compact('orders'));
    }

    public function showOrder(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return view('store.account.orders.show', [
            'order' => $order->load(['items.product', 'shipments', 'returnRequests', 'payments', 'paymentReceipts']),
        ]);
    }

    public function storeReturn(ReturnRequestFormRequest $request, Order $order): RedirectResponse
    {
        ReturnRequest::query()->create([
            'order_id' => $order->id,
            'order_item_id' => $request->validated('order_item_id'),
            'user_id' => $request->user()->id,
            'reason' => $request->validated('reason'),
            'status' => ReturnRequestStatus::Pending,
        ]);

        return back()->with('success', 'Solicitud de devolución enviada. Te contactaremos pronto.');
    }

    public function offers(Request $request): View
    {
        $offers = $request->user()
            ->offers()
            ->with('product')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('store.account.offers.index', compact('offers'));
    }

    public function acceptOffer(Request $request, Offer $offer): RedirectResponse
    {
        $this->assertOfferOwner($request, $offer);
        abort_unless($offer->status === OfferStatus::Countered && $offer->expires_at?->isFuture(), 422, 'La contraoferta ya no está vigente.');

        $offer->update([
            'status' => OfferStatus::Accepted,
            'accepted_amount' => $offer->counter_amount,
            'responded_at' => now(),
            'expires_at' => now()->addHours((int) config('store.offer_acceptance_hours', 48)),
        ]);
        Mail::to(config('mail.from.address'))->queue((new OfferBuyerResponded($offer->fresh(['product', 'user']), 'accepted'))->afterCommit());

        return back()->with('success', 'Contraoferta aceptada.');
    }

    public function declineOffer(Request $request, Offer $offer): RedirectResponse
    {
        $this->assertOfferOwner($request, $offer);
        abort_unless($offer->status === OfferStatus::Countered && $offer->expires_at?->isFuture(), 422, 'La contraoferta ya no está vigente.');

        $offer->update(['status' => OfferStatus::Declined, 'responded_at' => now()]);
        Mail::to(config('mail.from.address'))->queue((new OfferBuyerResponded($offer->fresh(['product', 'user']), 'declined'))->afterCommit());

        return back()->with('success', 'Contraoferta rechazada.');
    }

    public function addOfferToCart(Request $request, Offer $offer, CartService $cartService): RedirectResponse
    {
        $this->assertOfferOwner($request, $offer);
        abort_unless($offer->status === OfferStatus::Accepted && $offer->expires_at?->isFuture() && ! $offer->order_id, 422, 'El precio acordado ya no está vigente.');

        $cart = $cartService->resolveCart($request->user(), null);
        $cartService->addItem($cart, $offer->product, 1);

        return redirect()->route('cart.index')->with('success', 'Producto agregado con tu precio acordado.');
    }

    private function assertOfferOwner(Request $request, Offer $offer): void
    {
        abort_unless($offer->user_id === $request->user()->id, 403);
    }
}
