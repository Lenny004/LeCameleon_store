@extends('layouts.admin')

@section('title', 'Order ' . $order->number)
@section('page-title', 'Order #' . $order->number)

@section('content')
@php
    $shipping = $order->shipping_address ?? [];
    $shippingLine = collect([
        $shipping['line1'] ?? null,
        $shipping['city'] ?? null,
        $shipping['postal_code'] ?? null,
    ])->filter()->implode(', ');
    $orderStatusLabels = ['pending' => 'Pendiente', 'paid' => 'Pagado', 'processing' => 'En preparación', 'shipped' => 'Enviado', 'delivered' => 'Entregado', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado'];
    $shipmentStatusLabels = [
        'pending' => 'Pendiente',
        'scheduled' => 'Programado',
        'shipped' => 'Enviado',
        'picked_up' => 'Recogido',
        'in_transit' => 'En tránsito',
        'out_for_delivery' => 'En reparto',
        'delivered' => 'Entregado',
        'failed' => 'Fallido',
        'returned' => 'Devuelto',
        'returned_to_warehouse' => 'Devuelto al almacén',
        'cancelled' => 'Cancelado',
    ];
    $recipientOutcomeLabels = [
        'accepted' => 'Aceptado',
        'refused' => 'Rechazado',
        'no_answer' => 'Sin respuesta',
        'wrong_address' => 'Dirección incorrecta',
        'rescheduled' => 'Reprogramado',
        'left_with_neighbor' => 'Dejado con un vecino',
    ];
@endphp

<div class="admin-order-page">
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Datos del pedido</h2>
            <span class="badge badge--primary">{{ $orderStatusLabels[$order->status->value] ?? $order->status->value }}</span>
        </div>
        <p class="text-muted">Cliente: {{ $order->customerEmail() ?? $order->user?->email ?? 'Invitado' }}</p>
        @if ($shippingLine)
            <p class="text-muted admin-order__meta">Enviar a: {{ $shippingLine }}</p>
        @endif
        <p class="text-muted admin-order__meta">Creado: {{ $order->placed_at?->format('Y-m-d H:i') }}</p>
    </div>

    <div class="card">
        <h2 class="card__title admin-form__title admin-form__title--compact">Historial de estados</h2>
        @include('components.order-timeline', ['timeline' => $order->statusTimeline()])
    </div>

    <div class="card">
        <h2 class="card__title admin-form__title admin-form__title--compact">Artículos del pedido</h2>
        <div class="table-wrap">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->sku }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($order->coupon_code)
            <p class="text-muted admin-order__note">
                Coupon {{ $order->coupon_code }}: −${{ number_format((float) $order->discount_total, 2) }}
            </p>
        @endif
        <p class="admin-order__total">Total: ${{ number_format((float) $order->grand_total, 2) }}</p>
    </div>

    @if ($order->payments->isNotEmpty())
        <div class="card">
            <h2 class="card__title admin-form__title admin-form__title--compact">Pagos</h2>
            <div class="table-wrap">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>Proveedor</th>
                            <th>Estado</th>
                            <th>Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->payments as $payment)
                            <tr>
                                <td>{{ $payment->provider }}</td>
                                <td>{{ ucfirst($payment->status->value) }}</td>
                                <td>${{ number_format((float) $payment->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($order->status->value === 'pending' && $order->payments->contains(fn ($payment) => $payment->status->value === 'pending'))
                <form method="POST" action="{{ route('admin.orders.capture-payment', $order) }}" class="admin-order__payment-form">
                    @csrf
                    <button type="submit" class="btn btn--primary">Marcar pago como capturado</button>
                </form>
            @endif
        </div>
    @endif

    @if (Route::has('admin.orders.shipment'))
        @php
            $shipment = $shipment ?? $order->shipments->first();
        @endphp
        <div class="card logistics-shipment-panel">
            <h2 class="card__title admin-form__title admin-form__title--compact">Seguimiento del envío</h2>

            <form method="POST" action="{{ route('admin.orders.shipment', $order) }}">
                @csrf
                @method('PATCH')
                <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
                <div class="logistics-form__grid">
                    <div class="form-group">
                        <label class="form-label" for="logistics_company_id">Empresa transportista</label>
                        <select
                            id="logistics_company_id"
                            name="logistics_company_id"
                            class="form-select @error('logistics_company_id') form-select--error @enderror"
                            @error('logistics_company_id') aria-invalid="true" aria-describedby="logistics_company_id-error" @enderror
                        >
                            <option value="">— Sin asignar —</option>
                            @foreach ($companies ?? [] as $company)
                                <option value="{{ $company->id }}" @selected((string) old('logistics_company_id', $shipment?->logistics_company_id) === (string) $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('logistics_company_id')<span class="form-error" id="logistics_company_id-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="logistics_worker_id">Colaborador</label>
                        <select
                            id="logistics_worker_id"
                            name="logistics_worker_id"
                            class="form-select @error('logistics_worker_id') form-select--error @enderror"
                            @error('logistics_worker_id') aria-invalid="true" aria-describedby="logistics_worker_id-error" @enderror
                        >
                            <option value="">— Sin asignar —</option>
                            @foreach ($workers ?? [] as $worker)
                                <option value="{{ $worker->id }}" @selected((string) old('logistics_worker_id', $shipment?->logistics_worker_id) === (string) $worker->id)>{{ $worker->fullName() }}</option>
                            @endforeach
                        </select>
                        @error('logistics_worker_id')<span class="form-error" id="logistics_worker_id-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="logistics_vehicle_id">Vehículo</label>
                        <select
                            id="logistics_vehicle_id"
                            name="logistics_vehicle_id"
                            class="form-select @error('logistics_vehicle_id') form-select--error @enderror"
                            @error('logistics_vehicle_id') aria-invalid="true" aria-describedby="logistics_vehicle_id-error" @enderror
                        >
                            <option value="">— Sin asignar —</option>
                            @foreach ($vehicles ?? [] as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected((string) old('logistics_vehicle_id', $shipment?->logistics_vehicle_id) === (string) $vehicle->id)>{{ $vehicle->plate_number }}</option>
                            @endforeach
                        </select>
                        @error('logistics_vehicle_id')<span class="form-error" id="logistics_vehicle_id-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group logistics-form__full">
                        <label class="form-label" for="destination_municipality_id">Municipio de destino</label>
                        @include('components.municipality-select', [
                            'departments' => $departments ?? collect(),
                            'name' => 'destination_municipality_id',
                            'id' => 'destination_municipality_id',
                            'selected' => old('destination_municipality_id', $shipment?->destination_municipality_id),
                        ])
                        @error('destination_municipality_id')<span class="form-error" id="destination_municipality_id-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="carrier">Transportista</label>
                        <input
                            type="text"
                            id="carrier"
                            name="carrier"
                            class="form-input @error('carrier') form-input--error @enderror"
                            value="{{ old('carrier', $shipment?->carrier) }}"
                            placeholder="Mensajería Cameleon"
                            maxlength="100"
                            autocomplete="organization"
                            @error('carrier') aria-invalid="true" aria-describedby="carrier-error" @enderror
                        >
                        @error('carrier')<span class="form-error" id="carrier-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="tracking_number">Número de guía</label>
                        <input
                            type="text"
                            id="tracking_number"
                            name="tracking_number"
                            class="form-input @error('tracking_number') form-input--error @enderror"
                            value="{{ old('tracking_number', $shipment?->tracking_number) }}"
                            placeholder="LC-SV-ABC123"
                            maxlength="150"
                            autocomplete="off"
                            @error('tracking_number') aria-invalid="true" aria-describedby="tracking_number-error" @enderror
                        >
                        @error('tracking_number')<span class="form-error" id="tracking_number-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="shipment_status">Estado <span class="form-label__required" aria-hidden="true">*</span></label>
                        <select
                            id="shipment_status"
                            name="status"
                            class="form-select @error('status') form-select--error @enderror"
                            required
                            @error('status') aria-invalid="true" aria-describedby="shipment_status-error" @enderror
                        >
                            @foreach ($shipmentStatuses ?? [] as $shipmentStatus)
                                <option value="{{ $shipmentStatus->value }}" @selected(old('status', $shipment?->status?->value ?? 'pending') === $shipmentStatus->value)>
                                    {{ $shipmentStatusLabels[$shipmentStatus->value] ?? $shipmentStatus->value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @error('status')<span class="form-error" id="shipment_status-error">{{ $message }}</span>@enderror
                <button type="submit" class="btn btn--primary admin-form__submit admin-form__submit--compact-spaced">Guardar asignación</button>
            </form>

            @if ($shipment?->events?->isNotEmpty())
                <div class="logistics-shipment-panel__events">
                    <h3 class="card__title admin-form__title admin-form__title--small">Historial de eventos</h3>
                    <div class="logistics-timeline">
                        @foreach ($shipment->events->sortByDesc('happened_at') as $event)
                            <div class="logistics-timeline__item">
                                <strong>{{ $shipmentStatusLabels[$event->status->value] ?? $event->status->value }}</strong>
                                @if ($event->recipient_outcome)
                                    <span class="text-muted"> · {{ $recipientOutcomeLabels[$event->recipient_outcome->value] ?? $event->recipient_outcome->value }}</span>
                                @endif
                                <p class="logistics-timeline__meta">{{ $event->happened_at?->format('Y-m-d H:i') }}</p>
                                @if ($event->note)
                                    <p>{{ $event->note }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (Route::has('admin.orders.shipment-events'))
                <form method="POST" action="{{ route('admin.orders.shipment-events', $order) }}" class="admin-order__event-form">
                    @csrf
                    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
                    <h3 class="card__title admin-form__title admin-form__title--small admin-form__title--compact">Registrar evento</h3>
                    <div class="logistics-form__grid">
                        <div class="form-group">
                            <label class="form-label" for="event_status">Estado <span class="form-label__required" aria-hidden="true">*</span></label>
                            <select
                                id="event_status"
                                name="status"
                                class="form-select @error('status') form-select--error @enderror"
                                required
                                @error('status') aria-invalid="true" aria-describedby="event_status-error" @enderror
                            >
                                @foreach ($shipmentStatuses ?? [] as $shipmentStatus)
                                    <option value="{{ $shipmentStatus->value }}" @selected(old('status', 'pending') === $shipmentStatus->value)>
                                        {{ $shipmentStatusLabels[$shipmentStatus->value] ?? $shipmentStatus->value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')<span class="form-error" id="event_status-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="recipient_outcome">Resultado para el destinatario</label>
                            <select id="recipient_outcome" name="recipient_outcome" class="form-select @error('recipient_outcome') form-select--error @enderror" @error('recipient_outcome') aria-invalid="true" aria-describedby="recipient_outcome-error" @enderror>
                                <option value="">— No aplica —</option>
                                @foreach ($recipientOutcomes ?? [] as $outcome)
                                    <option value="{{ $outcome->value }}" @selected(old('recipient_outcome') === $outcome->value)>{{ $recipientOutcomeLabels[$outcome->value] ?? $outcome->value }}</option>
                                @endforeach
                            </select>
                            @error('recipient_outcome')<span class="form-error" id="recipient_outcome-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group logistics-form__full">
                            <label class="form-label" for="note">Nota</label>
                            <input
                                type="text"
                                id="note"
                                name="note"
                                class="form-input @error('note') form-input--error @enderror"
                                value="{{ old('note') }}"
                                placeholder="Indica qué ocurrió con la entrega."
                                maxlength="500"
                                @error('note') aria-invalid="true" aria-describedby="note-error" @enderror
                            >
                            @error('note')<span class="form-error" id="note-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn--ghost admin-form__submit admin-form__submit--compact-spaced">Registrar evento</button>
                </form>
            @endif
        </div>
    @endif

    @if (Route::has('admin.orders.status'))
        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="card">
            @csrf
            @method('PATCH')
            <h2 class="card__title admin-form__title admin-form__title--compact">Actualizar estado</h2>
            <div class="form-group">
                <label class="form-label" for="status">Estado</label>
                <select id="status" name="status" class="form-select @error('status') form-select--error @enderror" required @error('status') aria-invalid="true" aria-describedby="status-error" @enderror>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $orderStatusLabels[$status->value] ?? $status->value }}</option>
                    @endforeach
                </select>
            </div>
            @error('status')<span class="form-error" id="status-error">{{ $message }}</span>@enderror
            <button type="submit" class="btn btn--primary">Actualizar pedido</button>
        </form>
    @endif
</div>
@endsection
