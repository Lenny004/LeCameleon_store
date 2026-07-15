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
@endphp

<div style="display:grid;gap:var(--space-xl);max-width:48rem;">
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Order details</h2>
            <span class="badge badge--primary">{{ ucfirst($order->status->value) }}</span>
        </div>
        <p class="text-muted">Customer: {{ $order->customerEmail() ?? $order->user?->email ?? 'Guest' }}</p>
        @if ($shippingLine)
            <p class="text-muted" style="margin-top:var(--space-xs);">Ship to: {{ $shippingLine }}</p>
        @endif
        <p class="text-muted" style="margin-top:var(--space-xs);">Placed: {{ $order->placed_at?->format('Y-m-d H:i') }}</p>
    </div>

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-md);">Status timeline</h2>
        @include('components.order-timeline', ['timeline' => $order->statusTimeline()])
    </div>

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-md);">Line items</h2>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
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
            <p class="text-muted" style="margin-top:var(--space-md);">
                Coupon {{ $order->coupon_code }}: −${{ number_format((float) $order->discount_total, 2) }}
            </p>
        @endif
        <p style="text-align:right;margin-top:var(--space-md);font-weight:700;">Total: ${{ number_format((float) $order->grand_total, 2) }}</p>
    </div>

    @if ($order->payments->isNotEmpty())
        <div class="card">
            <h2 class="card__title" style="margin-bottom:var(--space-md);">Payments</h2>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Provider</th>
                            <th>Status</th>
                            <th>Amount</th>
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
                <form method="POST" action="{{ route('admin.orders.capture-payment', $order) }}" style="margin-top:var(--space-md);">
                    @csrf
                    <button type="submit" class="btn btn--primary">Mark payment captured</button>
                </form>
            @endif
        </div>
    @endif

    @if (Route::has('admin.orders.shipment'))
        @php
            $shipment = $shipment ?? $order->shipments->first();
        @endphp
        <div class="card logistics-shipment-panel">
            <h2 class="card__title" style="margin-bottom:var(--space-md);">Shipment tracking</h2>

            <form method="POST" action="{{ route('admin.orders.shipment', $order) }}">
                @csrf
                @method('PATCH')
                <div class="logistics-form__grid">
                    <div class="form-group">
                        <label class="form-label" for="logistics_company_id">Carrier company</label>
                        <select id="logistics_company_id" name="logistics_company_id" class="form-select">
                            <option value="">— Unassigned —</option>
                            @foreach ($companies ?? [] as $company)
                                <option value="{{ $company->id }}" @selected((string) old('logistics_company_id', $shipment?->logistics_company_id) === (string) $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="logistics_worker_id">Worker</label>
                        <select id="logistics_worker_id" name="logistics_worker_id" class="form-select">
                            <option value="">— Unassigned —</option>
                            @foreach ($workers ?? [] as $worker)
                                <option value="{{ $worker->id }}" @selected((string) old('logistics_worker_id', $shipment?->logistics_worker_id) === (string) $worker->id)>{{ $worker->fullName() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="logistics_vehicle_id">Vehicle</label>
                        <select id="logistics_vehicle_id" name="logistics_vehicle_id" class="form-select">
                            <option value="">— Unassigned —</option>
                            @foreach ($vehicles ?? [] as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected((string) old('logistics_vehicle_id', $shipment?->logistics_vehicle_id) === (string) $vehicle->id)>{{ $vehicle->plate_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group logistics-form__full">
                        <label class="form-label" for="destination_municipality_id">Destination municipality</label>
                        @include('components.municipality-select', [
                            'departments' => $departments ?? collect(),
                            'name' => 'destination_municipality_id',
                            'id' => 'destination_municipality_id',
                            'selected' => old('destination_municipality_id', $shipment?->destination_municipality_id),
                        ])
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="carrier">Carrier label</label>
                        <input type="text" id="carrier" name="carrier" class="form-input" value="{{ old('carrier', $shipment?->carrier) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="tracking_number">Tracking number</label>
                        <input type="text" id="tracking_number" name="tracking_number" class="form-input" value="{{ old('tracking_number', $shipment?->tracking_number) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="shipment_status">Status</label>
                        <select id="shipment_status" name="status" class="form-select" required>
                            @foreach ($shipmentStatuses ?? [] as $shipmentStatus)
                                <option value="{{ $shipmentStatus->value }}" @selected(old('status', $shipment?->status?->value ?? 'pending') === $shipmentStatus->value)>
                                    {{ ucfirst(str_replace('_', ' ', $shipmentStatus->value)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn--primary" style="margin-top:var(--space-md);">Save assignment</button>
            </form>

            @if ($shipment?->events?->isNotEmpty())
                <div class="logistics-shipment-panel__events">
                    <h3 class="card__title" style="font-size:0.9375rem;margin-bottom:var(--space-sm);">Event timeline</h3>
                    <div class="logistics-timeline">
                        @foreach ($shipment->events->sortByDesc('happened_at') as $event)
                            <div class="logistics-timeline__item">
                                <strong>{{ ucfirst(str_replace('_', ' ', $event->status->value)) }}</strong>
                                @if ($event->recipient_outcome)
                                    <span class="text-muted"> · {{ ucfirst(str_replace('_', ' ', $event->recipient_outcome->value)) }}</span>
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
                <form method="POST" action="{{ route('admin.orders.shipment-events', $order) }}" style="margin-top:var(--space-lg);padding-top:var(--space-lg);border-top:1px solid var(--border);">
                    @csrf
                    <h3 class="card__title" style="font-size:0.9375rem;margin-bottom:var(--space-md);">Append event</h3>
                    <div class="logistics-form__grid">
                        <div class="form-group">
                            <label class="form-label" for="event_status">Status</label>
                            <select id="event_status" name="status" class="form-select" required>
                                @foreach ($shipmentStatuses ?? [] as $shipmentStatus)
                                    <option value="{{ $shipmentStatus->value }}">{{ ucfirst(str_replace('_', ' ', $shipmentStatus->value)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="recipient_outcome">Recipient outcome</label>
                            <select id="recipient_outcome" name="recipient_outcome" class="form-select">
                                <option value="">— N/A —</option>
                                @foreach ($recipientOutcomes ?? [] as $outcome)
                                    <option value="{{ $outcome->value }}">{{ ucfirst(str_replace('_', ' ', $outcome->value)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group logistics-form__full">
                            <label class="form-label" for="note">Note</label>
                            <input type="text" id="note" name="note" class="form-input" maxlength="500">
                        </div>
                    </div>
                    <button type="submit" class="btn btn--ghost" style="margin-top:var(--space-md);">Record event</button>
                </form>
            @endif
        </div>
    @endif

    @if (Route::has('admin.orders.status'))
        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="card">
            @csrf
            @method('PATCH')
            <h2 class="card__title" style="margin-bottom:var(--space-md);">Update status</h2>
            <div class="form-group">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected($order->status === $status)>{{ ucfirst($status->value) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn--primary">Update order</button>
        </form>
    @endif
</div>
@endsection
