@extends('layouts.store')

@section('title', 'Pedido #' . $order->number . ' — Le Cameleon')

@section('content')
@php
    $shipping = $order->shipping_address ?? [];
    $shippingLine = collect([
        $shipping['line1'] ?? null,
        $shipping['city'] ?? null,
        $shipping['postal_code'] ?? null,
    ])->filter()->implode(', ');
@endphp

<div class="container" style="padding-block:var(--space-xl);">
    <nav class="breadcrumb">
        @if (Route::has('account.orders.index'))
            <a href="{{ route('account.orders.index') }}">Mis pedidos</a>
            <span class="breadcrumb__sep">/</span>
        @endif
        <span>#{{ $order->number }}</span>
    </nav>

    <div class="account-content" style="max-width:40rem;">
        <div class="order-card">
            <div class="order-card__header">
                <div>
                    <h1 class="heading-2">Pedido #{{ $order->number }}</h1>
                    <p class="order-card__date">{{ $order->placed_at?->format('d/m/Y H:i') }}</p>
                </div>
                <span class="badge badge--primary">{{ ucfirst($order->status->value) }}</span>
            </div>

            <section style="margin:var(--space-lg) 0;">
                <h2 class="text-small" style="font-weight:700;margin-bottom:var(--space-sm);">Estado del pedido</h2>
                @include('components.order-timeline', ['timeline' => $order->statusTimeline()])
            </section>

            @if ($shippingLine)
                <p class="text-muted" style="margin:var(--space-md) 0;">Envío a: {{ $shippingLine }}</p>
            @endif

            @if ($order->shipments->isNotEmpty())
                <section style="margin:var(--space-lg) 0;">
                    <h2 class="text-small" style="font-weight:700;margin-bottom:var(--space-sm);">Seguimiento de envío</h2>
                    @foreach ($order->shipments as $shipment)
                        <dl class="product-info__specs">
                            @if ($shipment->carrier)
                                <div class="product-info__spec"><dt>Transportista</dt><dd>{{ $shipment->carrier }}</dd></div>
                            @endif
                            @if ($shipment->tracking_number)
                                <div class="product-info__spec"><dt>Número de guía</dt><dd>{{ $shipment->tracking_number }}</dd></div>
                            @endif
                            <div class="product-info__spec"><dt>Estado</dt><dd>{{ ucfirst(str_replace('_', ' ', $shipment->status->value)) }}</dd></div>
                            @if ($shipment->shipped_at)
                                <div class="product-info__spec"><dt>Enviado</dt><dd>{{ $shipment->shipped_at->format('d/m/Y H:i') }}</dd></div>
                            @endif
                        </dl>
                    @endforeach
                </section>
            @endif

            @foreach ($order->items as $item)
                <div class="checkout-review-item">
                    <span>{{ $item->name }} × {{ $item->quantity }}</span>
                    <span>${{ number_format((float) $item->line_total, 2) }}</span>
                </div>
            @endforeach

            @if ($order->coupon_code)
                <div class="checkout-review-item">
                    <span>Cupón ({{ $order->coupon_code }})</span>
                    <span>−${{ number_format((float) $order->discount_total, 2) }}</span>
                </div>
            @endif

            <div class="order-card__footer">
                <span class="order-card__total">Total: ${{ number_format((float) $order->grand_total, 2) }}</span>
            </div>
        </div>

        @php
            $hasPendingReturn = $order->returnRequests->contains(fn ($request) => $request->status->value === 'pending');
        @endphp

        @if (Route::has('account.orders.returns.store') && ! $hasPendingReturn)
            <div class="order-card" style="margin-top:var(--space-lg);">
                <h2 class="text-small" style="font-weight:700;margin-bottom:var(--space-md);">Solicitar devolución</h2>
                <p class="text-muted" style="margin-bottom:var(--space-md);">
                    Consulta nuestra <a href="{{ route('returns') }}">política de devoluciones</a> antes de enviar tu solicitud.
                </p>
                <form method="POST" action="{{ route('account.orders.returns.store', $order) }}">
                    @csrf
                    @if ($order->items->count() > 1)
                        <div class="form-group">
                            <label class="form-label" for="order_item_id">Artículo (opcional)</label>
                            <select id="order_item_id" name="order_item_id" class="form-select">
                                <option value="">Todo el pedido</option>
                                @foreach ($order->items as $item)
                                    <option value="{{ $item->id }}" @selected(old('order_item_id') == $item->id)>{{ $item->name }} × {{ $item->quantity }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="form-group">
                        <label class="form-label" for="reason">Motivo</label>
                        <textarea id="reason" name="reason" class="form-textarea" rows="4" required>{{ old('reason') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn--ghost">Enviar solicitud</button>
                </form>
            </div>
        @elseif ($hasPendingReturn)
            <p class="text-muted" style="margin-top:var(--space-lg);">Tienes una solicitud de devolución en revisión.</p>
        @endif
    </div>
</div>
@endsection
