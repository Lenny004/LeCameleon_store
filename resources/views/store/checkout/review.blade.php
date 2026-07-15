@extends('layouts.store')

@section('title', 'Revisar pedido — Le Cameleon')

@section('content')
<div class="container checkout">
    <h1 class="heading-1" style="margin-bottom: var(--space-lg);">Revisar pedido</h1>

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
                <p class="text-muted">{{ $shippingAddress ?? 'Calle Ejemplo 123, Ciudad, CP 12345' }}</p>
                @if (Route::has('checkout.index'))
                    <a href="{{ route('checkout.index') }}" class="text-small" style="color:var(--accent);margin-top:var(--space-sm);display:inline-block;">Editar</a>
                @endif
            </section>

            <section class="checkout-section">
                <h2 class="checkout-section__title">Artículos</h2>
                @foreach ($cartItems ?? [['name' => 'Chaqueta Denim 80s', 'qty' => 1, 'price' => 89.00], ['name' => 'Vestido Floral 70s', 'qty' => 1, 'price' => 120.00]] as $item)
                    <div class="checkout-review-item">
                        <span>{{ is_array($item) ? $item['name'] : $item->name }} × {{ is_array($item) ? $item['qty'] : $item->quantity }}</span>
                        <span>${{ number_format(is_array($item) ? $item['price'] * $item['qty'] : $item->price * $item->quantity, 2) }}</span>
                    </div>
                @endforeach
            </section>

            @if (Route::has('checkout.place'))
                <form method="POST" action="{{ route('checkout.place') }}">
                    @csrf
                    <button type="submit" class="btn btn--primary btn--lg">Confirmar pedido</button>
                </form>
            @endif
        </div>

        <aside class="cart-summary">
            @php $subtotal = $subtotal ?? 209.00; $shipping = $shipping ?? 9.99; @endphp
            <div class="cart-summary__row">
                <span>Subtotal</span>
                <span>${{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="cart-summary__row">
                <span>Envío</span>
                <span>${{ number_format($shipping, 2) }}</span>
            </div>
            <div class="cart-summary__row cart-summary__row--total">
                <span>Total</span>
                <span>${{ number_format($subtotal + $shipping, 2) }}</span>
            </div>
        </aside>
    </div>
</div>
@endsection
