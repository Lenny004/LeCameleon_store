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
        <form method="POST" action="{{ route('admin.orders.shipment', $order) }}" class="card">
            @csrf
            @method('PATCH')
            <h2 class="card__title" style="margin-bottom:var(--space-md);">Shipment tracking</h2>
            <div class="form-row form-row--2">
                <div class="form-group">
                    <label class="form-label" for="carrier">Carrier</label>
                    <input type="text" id="carrier" name="carrier" class="form-input" value="{{ old('carrier', $shipment?->carrier) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="tracking_number">Tracking number</label>
                    <input type="text" id="tracking_number" name="tracking_number" class="form-input" value="{{ old('tracking_number', $shipment?->tracking_number) }}">
                </div>
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
            <button type="submit" class="btn btn--primary">Save shipment</button>
        </form>
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
