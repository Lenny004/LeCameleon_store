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

<div class="container account-page-shell">
    <nav class="breadcrumb">
        @if (Route::has('account.orders.index'))
            <a class="breadcrumb__link" href="{{ route('account.orders.index') }}">Mis pedidos</a>
            <span class="breadcrumb__sep">/</span>
        @endif
        <span>#{{ $order->number }}</span>
    </nav>

    <div class="account-content account-content--narrow">
        <div class="order-card">
            <div class="order-card__header">
                <div>
                    <h1 class="heading-2">Pedido #{{ $order->number }}</h1>
                    <p class="order-card__date">{{ $order->placed_at?->format('d/m/Y H:i') }}</p>
                </div>
                <span class="badge badge--primary">{{ ucfirst($order->status->value) }}</span>
            </div>

            <section class="account-order__section">
                <h2 class="text-small account-order__section-title">Estado del pedido</h2>
                @include('components.order-timeline', ['timeline' => $order->statusTimeline()])
            </section>

            @if ($shippingLine)
                <p class="text-muted account-order__address">Envío a: {{ $shippingLine }}</p>
            @endif

            @if ($order->shipments->isNotEmpty())
                <section class="account-order__section">
                    <h2 class="text-small account-order__section-title">Seguimiento de envío</h2>
                    @foreach ($order->shipments as $shipment)
                        <dl class="product-info__specs">
                            @if ($shipment->carrier)
                                <div class="product-info__spec"><dt class="product-info__spec-term">Transportista</dt><dd class="product-info__spec-value">{{ $shipment->carrier }}</dd></div>
                            @endif
                            @if ($shipment->tracking_number)
                                <div class="product-info__spec"><dt class="product-info__spec-term">Número de guía</dt><dd class="product-info__spec-value">{{ $shipment->tracking_number }}</dd></div>
                            @endif
                            <div class="product-info__spec"><dt class="product-info__spec-term">Estado</dt><dd class="product-info__spec-value">{{ ucfirst(str_replace('_', ' ', $shipment->status->value)) }}</dd></div>
                            @if ($shipment->shipped_at)
                                <div class="product-info__spec"><dt class="product-info__spec-term">Enviado</dt><dd class="product-info__spec-value">{{ $shipment->shipped_at->format('d/m/Y H:i') }}</dd></div>
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
            <div class="order-card account-order__return">
                <h2 class="text-small account-order__return-title">Solicitar devolución</h2>
                <p class="text-muted account-order__return-description">
                    Consulta nuestra <a href="{{ route('returns') }}">política de devoluciones</a> antes de enviar tu solicitud.
                </p>
                <form method="POST" action="{{ route('account.orders.returns.store', $order) }}">
                    @csrf
                    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
                    @if ($order->items->count() > 1)
                        <div class="form-group">
                            <label class="form-label" for="order_item_id">Artículo (opcional)</label>
                            <select id="order_item_id" name="order_item_id" class="form-select @error('order_item_id') form-select--error @enderror" @error('order_item_id') aria-invalid="true" aria-describedby="order_item_id-error" @enderror>
                                <option value="">Todo el pedido</option>
                                @foreach ($order->items as $item)
                                    <option value="{{ $item->id }}" @selected(old('order_item_id') == $item->id)>{{ $item->name }} × {{ $item->quantity }}</option>
                                @endforeach
                            </select>
                            @error('order_item_id')<span class="form-error" id="order_item_id-error">{{ $message }}</span>@enderror
                        </div>
                    @endif
                    <div class="form-group">
                        <label class="form-label" for="reason">Motivo y detalles <span class="form-label__required" aria-hidden="true">*</span></label>
                        <textarea id="reason" name="reason" class="form-textarea @error('reason') form-textarea--error @enderror" rows="4" maxlength="2000" placeholder="Describe el motivo de tu solicitud y los detalles relevantes." required @error('reason') aria-invalid="true" aria-describedby="reason-error" @enderror>{{ old('reason') }}</textarea>
                        @error('reason')<span class="form-error" id="reason-error">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn btn--ghost">Enviar solicitud</button>
                </form>
            </div>
        @elseif ($hasPendingReturn)
            <p class="text-muted account-order__pending">Tienes una solicitud de devolución en revisión.</p>
        @endif
    </div>
</div>
@endsection
