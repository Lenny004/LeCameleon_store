<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly PaymentService $paymentService,
    ) {}

    public function index(): View
    {
        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->orderByDesc('placed_at')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load(['user', 'items.product', 'payments', 'shipments']),
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function updateStatus(OrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $this->orderService->transition(
            $order,
            OrderStatus::from($request->validated('status')),
            $request->user(),
        );

        return back()->with('success', 'Order status updated.');
    }

    public function capturePayment(Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $this->paymentService->capturePayment($order, request()->user());

        return back()->with('success', 'Payment captured and order marked as paid.');
    }
}
