<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\RecipientOutcome;
use App\Enums\ShipmentStatus;
use App\Http\Controllers\Concerns\LoadsServiceableMunicipalities;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStatusRequest;
use App\Http\Requests\Admin\ShipmentEventRequest;
use App\Http\Requests\Admin\ShipmentRequest;
use App\Models\LogisticsCompany;
use App\Models\LogisticsVehicle;
use App\Models\LogisticsWorker;
use App\Models\Order;
use App\Models\ShipmentEvent;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Support\RecordsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    use LoadsServiceableMunicipalities;

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
        $order->load([
            'user',
            'items.product',
            'payments',
            'shipments.events.author',
            'shipments.company',
            'shipments.worker',
            'shipments.vehicle',
            'shipments.destinationMunicipality',
        ]);

        $shipment = $order->shipments->first();

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => OrderStatus::cases(),
            'shipmentStatuses' => ShipmentStatus::cases(),
            'recipientOutcomes' => RecipientOutcome::cases(),
            'shipment' => $shipment,
            'companies' => LogisticsCompany::query()->where('is_active', true)->orderBy('name')->get(),
            'workers' => LogisticsWorker::query()->where('is_active', true)->orderBy('last_name')->get(),
            'vehicles' => LogisticsVehicle::query()->where('is_active', true)->orderBy('plate_number')->get(),
            'departments' => $this->serviceableDepartmentsWithMunicipalities(),
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

        RecordsActivity::log('payment.captured', $order);

        return back()->with('success', 'Payment captured and order marked as paid.');
    }

    public function updateShipment(ShipmentRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validated();
        $status = ShipmentStatus::from($validated['status']);

        $shipment = $order->shipments()->firstOrNew();
        $shipment->fill([
            'logistics_company_id' => $validated['logistics_company_id'] ?? null,
            'logistics_worker_id' => $validated['logistics_worker_id'] ?? null,
            'logistics_vehicle_id' => $validated['logistics_vehicle_id'] ?? null,
            'destination_municipality_id' => $validated['destination_municipality_id'] ?? null,
            'carrier' => $validated['carrier'] ?? null,
            'tracking_number' => $validated['tracking_number'] ?? null,
            'status' => $status,
        ]);

        if (in_array($status, [ShipmentStatus::Shipped, ShipmentStatus::InTransit, ShipmentStatus::OutForDelivery], true) && ! $shipment->shipped_at) {
            $shipment->shipped_at = now();
        }

        if ($status === ShipmentStatus::Delivered && ! $shipment->delivered_at) {
            $shipment->delivered_at = now();
        }

        $shipment->save();

        return back()->with('success', 'Shipment assignment saved.');
    }

    public function storeShipmentEvent(ShipmentEventRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validated();
        $status = ShipmentStatus::from($validated['status']);

        $shipment = $order->shipments()->firstOrCreate([], [
            'status' => ShipmentStatus::Pending,
        ]);

        $outcome = isset($validated['recipient_outcome'])
            ? RecipientOutcome::from($validated['recipient_outcome'])
            : null;

        ShipmentEvent::query()->create([
            'shipment_id' => $shipment->id,
            'status' => $status,
            'recipient_outcome' => $outcome,
            'note' => $validated['note'] ?? null,
            'happened_at' => now(),
            'created_by' => $request->user()?->id,
        ]);

        $shipment->update(['status' => $status]);

        if ($status === ShipmentStatus::Delivered && ! $shipment->delivered_at) {
            $shipment->update(['delivered_at' => now()]);
        }

        return back()->with('success', 'Shipment event recorded.');
    }
}
