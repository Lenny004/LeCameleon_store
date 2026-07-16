@extends('layouts.store')

@section('title', 'Pedido confirmado — Le Cameleon')

@section('content')
<div class="container checkout">
    <header class="checkout__header">
        <h1 class="heading-2">Pedido confirmado</h1>
    </header>

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
                <div class="checkout-success">
                    <span class="checkout-success__badge">Pedido recibido</span>
                    <p class="checkout-success__order">Pedido #{{ $order->number }}</p>
                    <p class="checkout-success__message">Gracias por tu compra. Te enviaremos actualizaciones por correo electrónico a medida que preparemos tu pedido.</p>
                </div>
            </section>

            <section class="checkout-section">
                <h2 class="checkout-section__title">Artículos</h2>
                @foreach ($order->items as $item)
                    <div class="checkout-review-item">
                        <span class="checkout-review-item__name">{{ $item->name }} × {{ $item->quantity }}</span>
                        <span class="checkout-review-item__price">${{ number_format((float) $item->line_total, 2) }}</span>
                    </div>
                @endforeach
            </section>

            <div class="checkout-success__actions">
                @if (Route::has('account.orders.show'))
                    <a href="{{ route('account.orders.show', $order) }}" class="btn btn--primary">Ver pedido en mi cuenta</a>
                @endif
                @if (Route::has('shop.index'))
                    <a href="{{ route('shop.index') }}" class="btn btn--secondary">Seguir comprando</a>
                @endif
            </div>
        </div>

        <aside class="order-summary">
            <h2 class="order-summary__title">Resumen del pedido</h2>
            <div class="cart-summary__rows">
                <div class="cart-summary__row">
                    <span class="cart-summary__row-label">Subtotal</span>
                    <span class="cart-summary__row-value">${{ number_format((float) $order->subtotal, 2) }}</span>
                </div>
                @if ($order->coupon_code)
                    <div class="cart-summary__row">
                        <span class="cart-summary__row-label">Cupón ({{ $order->coupon_code }})</span>
                        <span class="cart-summary__row-value">−${{ number_format((float) $order->discount_total, 2) }}</span>
                    </div>
                @endif
                <div class="cart-summary__row">
                    <span class="cart-summary__row-label">Envío</span>
                    <span class="cart-summary__row-value">${{ number_format((float) $order->shipping_total, 2) }}</span>
                </div>
                @if ((float) $order->tax_total > 0)
                    <div class="cart-summary__row">
                        <span class="cart-summary__row-label">Impuestos</span>
                        <span class="cart-summary__row-value">${{ number_format((float) $order->tax_total, 2) }}</span>
                    </div>
                @endif
                <div class="cart-summary__row cart-summary__row--total">
                    <span class="cart-summary__row-label">Total</span>
                    <span class="cart-summary__row-value">${{ number_format((float) $order->grand_total, 2) }}</span>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
