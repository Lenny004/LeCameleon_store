@extends('layouts.store')

@section('title', 'Revisar pedido — Le Cameleon')

@section('content')
<div class="container checkout">
    <header class="checkout__header">
        <h1 class="heading-2">Revisar pedido</h1>
    </header>

    <nav class="checkout-steps" aria-label="Pasos del checkout">
        <span class="checkout-step checkout-step--done">
            <span class="checkout-step__num">1</span>
            Envío
        </span>
        <span class="checkout-step checkout-step--active">
            <span class="checkout-step__num">2</span>
            Revisión
        </span>
        <span class="checkout-step">
            <span class="checkout-step__num">3</span>
            Confirmación
        </span>
    </nav>

    <div class="checkout-layout">
        <div class="checkout-form">
            <section class="checkout-section">
                <h2 class="checkout-section__title">Dirección de envío</h2>
                <p class="checkout-section__address">{{ $shippingAddress ?? 'Calle Ejemplo 123, Ciudad, CP 12345' }}</p>
                @if (Route::has('checkout.index'))
                    <a href="{{ route('checkout.index') }}" class="checkout-section__edit">Editar dirección</a>
                @endif
            </section>

            <section class="checkout-section">
                <h2 class="checkout-section__title">Artículos</h2>
                @foreach ($cartItems ?? [['name' => 'Chaqueta Denim 80s', 'qty' => 1, 'price' => 89.00], ['name' => 'Vestido Floral 70s', 'qty' => 1, 'price' => 120.00]] as $item)
                    <div class="checkout-review-item">
                        <span class="checkout-review-item__name">{{ is_array($item) ? $item['name'] : $item->name }} × {{ is_array($item) ? $item['qty'] : $item->quantity }}</span>
                        <span class="checkout-review-item__price">${{ number_format(is_array($item) ? $item['price'] * $item['qty'] : $item->price * $item->quantity, 2) }}</span>
                    </div>
                @endforeach
            </section>

            @if (Route::has('checkout.place'))
                <div class="checkout-submit">
                    <form method="POST" action="{{ route('checkout.place') }}">
                        @csrf
                        <button type="submit" class="btn btn--primary btn--lg">Confirmar pedido</button>
                    </form>
                    <p class="checkout-submit__note">Revisa los datos antes de confirmar. Podrás seguir el estado del pedido desde tu cuenta.</p>
                </div>
            @endif
        </div>

        <aside class="order-summary">
            <h2 class="order-summary__title">Resumen del pedido</h2>
            @php $subtotal = $subtotal ?? 209.00; $shipping = $shipping ?? 9.99; @endphp
            <div class="cart-summary__rows">
                <div class="cart-summary__row">
                    <span class="cart-summary__row-label">Subtotal</span>
                    <span class="cart-summary__row-value">${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="cart-summary__row">
                    <span class="cart-summary__row-label">Envío</span>
                    <span class="cart-summary__row-value">${{ number_format($shipping, 2) }}</span>
                </div>
                <div class="cart-summary__row cart-summary__row--total">
                    <span class="cart-summary__row-label">Total</span>
                    <span class="cart-summary__row-value">${{ number_format($subtotal + $shipping, 2) }}</span>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
