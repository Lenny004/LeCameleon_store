<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\RecipientOutcome;
use App\Enums\ShipmentStatus;
use App\Http\Controllers\Concerns\LoadsServiceableMunicipalities;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManualOrderRequest;
use App\Http\Requests\Admin\OrderFilterRequest;
use App\Http\Requests\Admin\OrderStatusRequest;
use App\Http\Requests\Admin\ShipmentEventRequest;
use App\Http\Requests\Admin\ShipmentRequest;
use App\Mail\PaymentReceiptRejected;
use App\Models\Cart;
use App\Models\LogisticsCompany;
use App\Models\LogisticsVehicle;
use App\Models\LogisticsWorker;
use App\Models\Order;
use App\Models\PaymentReceipt;
use App\Models\Product;
use App\Models\ShipmentEvent;
use App\Models\SvMunicipality;
use App\Models\User;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Support\RecordsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    use LoadsServiceableMunicipalities;

    public function __construct(
        private readonly OrderService $orderService,
        private readonly PaymentService $paymentService,
        private readonly CartService $cartService,
        private readonly CheckoutService $checkoutService,
    ) {}

    public function index(OrderFilterRequest $request): View
    {
        $orders = $this->filteredQuery($request)
            ->with('user')
            ->withCount('items')
            ->orderByDesc('placed_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function export(OrderFilterRequest $request): StreamedResponse
    {
        $orders = $this->filteredQuery($request)
            ->with(['user', 'payments'])
            ->withCount('items')
            ->orderByDesc('placed_at');
        $municipalities = SvMunicipality::query()->pluck('name', 'id');

        return response()->streamDownload(function () use ($orders, $municipalities): void {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['número', 'fecha', 'cliente', 'email', 'estado', 'pago', 'subtotal', 'descuento', 'envío', 'total', 'municipio']);
            foreach ($orders->lazy(100) as $order) {
                $payment = $order->payments->sortByDesc('created_at')->first();
                $method = PaymentMethod::tryFrom($order->paymentMethod());
                $paymentStatus = $payment?->status instanceof PaymentStatus
                    ? $payment->status->label()
                    : PaymentStatus::Pending->label();
                $municipality = $municipalities->get(data_get($order->shipping_address, 'sv_municipality_id'));
                $row = [
                    $order->number,
                    $order->placed_at?->format('Y-m-d H:i:s'),
                    $order->user?->name ?? data_get($order->shipping_address, 'first_name'),
                    $order->customerEmail(),
                    $order->status->label(),
                    ($method?->label() ?? $order->paymentMethod()).'/'.$paymentStatus,
                    $order->subtotal,
                    $order->discount_total,
                    $order->shipping_total,
                    $order->grand_total,
                    $municipality,
                ];
                fputcsv($handle, array_map(fn ($value): string => $this->neutralizeCsvFormula($value), $row));
            }
            fclose($handle);
        }, 'pedidos.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function print(Order $order): View
    {
        $order->load(['user', 'items', 'shipments']);
        $municipality = SvMunicipality::query()->find(data_get($order->shipping_address, 'sv_municipality_id'));

        return view('admin.orders.print', compact('order', 'municipality'));
    }

    public function create(): View
    {
        return view('admin.orders.create', ['products' => Product::query()->where('status', 'published')->whereColumn('quantity_available', '>', 'quantity_reserved')->orderBy('name')->get(), 'departments' => $this->serviceableDepartmentsWithMunicipalities()]);
    }

    public function storeManual(ManualOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = User::query()->where('email', $data['email'])->first();
        $cart = Cart::query()->create(['session_id' => 'admin-manual-'.Str::uuid()]);

        try {
            foreach ($data['items'] as $item) {
                if (empty($item['product_id'])) {
                    continue;
                }

                $product = Product::query()->findOrFail($item['product_id']);
                $this->cartService->addItem($cart, $product, (int) $item['quantity']);
            }

            $address = $data['shipping_address'];
            $address['email'] = $data['email'];
            $address['phone'] = $address['phone'] ?? $data['phone'] ?? null;
            $order = $this->checkoutService->placeOrder(
                $cart,
                $user,
                $data['email'],
                $address,
                $address,
                null,
                $data['notes'] ?? null,
                $data['payment_method'],
                $data['destination_municipality_id'] ?? null,
                (bool) ($data['send_email'] ?? false),
                $data['shipping_override'] ?? null,
            );

            if (! empty($data['mark_paid'])) {
                $this->paymentService->capturePayment($order, $request->user());
            }

            RecordsActivity::log('order.manual.created', $order, ['created_by' => $request->user()->id]);

            return redirect()->route('admin.orders.show', $order)->with('success', 'Pedido manual creado.');
        } finally {
            $cart->delete();
        }
    }

    private function neutralizeCsvFormula(mixed $value): string
    {
        $value = (string) ($value ?? '');

        return preg_match('/^[=+\-@]/', $value) ? "'".$value : $value;
    }

    private function filteredQuery(OrderFilterRequest $request): Builder
    {
        $filters = $request->validated();

        return Order::query()
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['from'] ?? null, fn ($q, $v) => $q->whereDate('placed_at', '>=', $v))
            ->when($filters['to'] ?? null, fn ($q, $v) => $q->whereDate('placed_at', '<=', $v))
            ->when($filters['payment_status'] ?? null, fn ($q, $v) => $q->whereHas('payments', fn ($p) => $p->where('status', $v)))
            ->when($filters['payment_method'] ?? null, fn ($q, $v) => $q->whereHas('payments', fn ($p) => $p->where('provider', $v)))
            ->when($filters['search'] ?? null, function ($q, $v): void {
                $q->where(function ($query) use ($v): void {
                    $query->where('number', 'like', "%{$v}%")
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%"))
                        ->orWhere('shipping_address->email', 'like', "%{$v}%");
                });
            });
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
            'paymentReceipts.user',
            'paymentReceipts.reviewer',
            'notes.user',
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

        return back()->with('success', 'Estado del pedido actualizado.');
    }

    public function capturePayment(Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $this->paymentService->capturePayment($order, request()->user());

        RecordsActivity::log('payment.captured', $order);

        return back()->with('success', 'Pago capturado y pedido marcado como pagado.');
    }

    public function downloadReceipt(Order $order, PaymentReceipt $receipt): Response
    {
        abort_unless($receipt->order_id === $order->id, 404);
        abort_unless(Storage::disk('local')->exists($receipt->path), 404);

        return Storage::disk('local')->download($receipt->path, $receipt->original_name);
    }

    public function acceptReceipt(Order $order, PaymentReceipt $receipt): RedirectResponse
    {
        $this->authorize('update', $order);
        abort_unless($receipt->order_id === $order->id && $receipt->status === 'pending', 422);

        if ($order->status !== OrderStatus::Paid) {
            $this->paymentService->capturePayment($order, request()->user());
        }

        $receipt->update(['status' => 'accepted', 'reviewed_by' => request()->user()->id, 'reviewed_at' => now()]);
        RecordsActivity::log('payment.receipt.accepted', $order, ['receipt_id' => $receipt->id]);

        return back()->with('success', 'Comprobante aceptado.');
    }

    public function rejectReceipt(Request $request, Order $order, PaymentReceipt $receipt): RedirectResponse
    {
        $this->authorize('update', $order);
        abort_unless($receipt->order_id === $order->id && $receipt->status === 'pending', 422);
        $data = $request->validate(['admin_notes' => ['required', 'string', 'max:2000']]);
        $receipt->update(['status' => 'rejected', 'admin_notes' => $data['admin_notes'], 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);
        $email = $order->customerEmail();
        if ($email) {
            Mail::to($email)->queue((new PaymentReceiptRejected($receipt->load('order')))->afterCommit());
        }
        RecordsActivity::log('payment.receipt.rejected', $order, ['receipt_id' => $receipt->id]);

        return back()->with('success', 'Comprobante rechazado.');
    }

    public function storeNote(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);
        $data = $request->validate(['body' => ['required', 'string', 'max:2000']]);
        $order->notes()->create(['user_id' => $request->user()->id, 'body' => $data['body']]);
        RecordsActivity::log('order.note.created', $order);

        return back()->with('success', 'Nota interna guardada.');
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

        return back()->with('success', 'Asignación de envío guardada.');
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

        return back()->with('success', 'Evento de envío registrado.');
    }
}
