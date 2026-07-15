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
    </div>
</div>
@endsection
