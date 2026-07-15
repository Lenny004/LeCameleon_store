<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\ProfileUpdateRequest;
use App\Models\Order;
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
            'order' => $order->load('items.product'),
        ]);
    }
}
