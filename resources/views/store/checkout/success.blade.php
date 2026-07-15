@extends('layouts.store')

@section('title', 'Pedido confirmado — Le Cameleon')

@section('content')
<div class="container checkout">
    <h1 class="heading-1" style="margin-bottom: var(--space-lg);">Pedido confirmado</h1>

    <nav class="checkout-steps" aria-label="Pasos del checkout">
        <span class="checkout-step checkout-step--done">
            <span class="checkout-step__num">1</span>
            Envío
        </span>
        <span class="checkout-step checkout-step--done">
            <span class="checkout-step__num">2</span>
            Revisión
        </span>
        <span class="checkout-step checkout-step--active">
            <span class="checkout-step__num">3</span>
            Confirmación
        </span>
    </nav>

    <div class="checkout-layout">
        <div class="checkout-form">
            <section class="checkout-section">
                <h2 class="checkout-section__title">Pedido #{{ $order->number }}</h2>
                <p class="text-muted">Gracias por tu compra. Te enviaremos actualizaciones por correo.</p>
            </section>

            <section class="checkout-section">
                <h2 class="checkout-section__title">Artículos</h2>
                @foreach ($order->items as $item)
                    <div class="checkout-review-item">
                        <span>{{ $item->name }} × {{ $item->quantity }}</span>
                        <span>${{ number_format((float) $item->line_total, 2) }}</span>
                    </div>
                @endforeach
            </section>

            @if (Route::has('account.orders.show'))
                <a href="{{ route('account.orders.show', $order) }}" class="btn btn--ghost">Ver pedido en mi cuenta</a>
            @endif
        </div>

        <aside class="cart-summary">
            <div class="cart-summary__row">
                <span>Subtotal</span>
                <span>${{ number_format((float) $order->subtotal, 2) }}</span>
            </div>
            @if ($order->coupon_code)
                <div class="cart-summary__row">
                    <span>Cupón ({{ $order->coupon_code }})</span>
                    <span>−${{ number_format((float) $order->discount_total, 2) }}</span>
                </div>
            @endif
            <div class="cart-summary__row">
                <span>Envío</span>
                <span>${{ number_format((float) $order->shipping_total, 2) }}</span>
            </div>
            @if ((float) $order->tax_total > 0)
                <div class="cart-summary__row">
                    <span>Impuestos</span>
                    <span>${{ number_format((float) $order->tax_total, 2) }}</span>
                </div>
            @endif
            <div class="cart-summary__row cart-summary__row--total">
                <span>Total</span>
                <span>${{ number_format((float) $order->grand_total, 2) }}</span>
            </div>
        </aside>
    </div>
</div>
@endsection
