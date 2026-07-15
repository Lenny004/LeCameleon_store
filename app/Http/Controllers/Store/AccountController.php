<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\ProfileUpdateRequest;
use App\Http\Requests\Store\ReturnRequestFormRequest;
use App\Enums\ReturnRequestStatus;
use App\Models\Order;
use App\Models\ReturnRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $request->user()->update($request->validated());

        return back()->with('success', 'Profile updated.');
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
            'order' => $order->load(['items.product', 'shipments', 'returnRequests']),
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
}
