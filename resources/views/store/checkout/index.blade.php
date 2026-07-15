@extends('layouts.store')

@section('title', 'Checkout — Le Cameleon')

@section('content')
<div class="container checkout">
    <h1 class="heading-1" style="margin-bottom: var(--space-lg);">Checkout</h1>

    <nav class="checkout-steps" aria-label="Pasos del checkout">
        <span class="checkout-step checkout-step--active">
            <span class="checkout-step__num">1</span>
            Envío
        </span>
        <span class="checkout-step">
            <span class="checkout-step__num">2</span>
            Revisión
        </span>
        <span class="checkout-step">
            <span class="checkout-step__num">3</span>
            Confirmación
        </span>
    </nav>

    <div class="checkout-layout">
        <form method="POST" action="{{ Route::has('checkout.store') ? route('checkout.store') : '#' }}" class="checkout-form">
            @csrf
            <section class="checkout-section">
                <h2 class="checkout-section__title">Datos de envío</h2>
                <div class="form-row form-row--2">
                    <div class="form-group">
                        <label class="form-label" for="first_name">Nombre</label>
                        <input type="text" id="first_name" name="first_name" class="form-input" value="{{ old('first_name', auth()->user()->name ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="last_name">Apellido</label>
                        <input type="text" id="last_name" name="last_name" class="form-input" value="{{ old('last_name') }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" class="form-input" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="address">Dirección</label>
                    <input type="text" id="address" name="address" class="form-input" value="{{ old('address') }}" required>
                </div>
                <div class="form-row form-row--2">
                    <div class="form-group">
                        <label class="form-label" for="city">Ciudad</label>
                        <input type="text" id="city" name="city" class="form-input" value="{{ old('city') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="postal_code">Código postal</label>
                        <input type="text" id="postal_code" name="postal_code" class="form-input" value="{{ old('postal_code') }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Teléfono</label>
                    <input type="tel" id="phone" name="phone" class="form-input" value="{{ old('phone') }}">
                </div>
            </section>

            <section class="checkout-section">
                <h2 class="checkout-section__title">Método de pago</h2>
                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="radio" name="payment_method" value="card" checked>
                        Tarjeta de crédito / débito
                    </label>
                </div>
                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="radio" name="payment_method" value="transfer">
                        Transferencia bancaria
                    </label>
                </div>
            </section>

            @if (Route::has('checkout.review'))
                <button type="submit" formaction="{{ route('checkout.review') }}" class="btn btn--primary btn--lg">Continuar a revisión</button>
            @else
                <button type="submit" class="btn btn--primary btn--lg">Continuar</button>
            @endif
        </form>

        <aside class="cart-summary">
            <h2 class="card__title" style="margin-bottom:var(--space-md);">Resumen</h2>
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
