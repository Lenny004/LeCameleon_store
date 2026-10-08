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
                    <x-brand-logo variant="compact" size="lg" class="checkout-success__logo" />
                    <span class="checkout-success__badge">Pedido recibido</span>
                    <p class="checkout-success__order">Pedido #{{ $order->number }}</p>
                    <p class="checkout-success__message">Gracias por tu compra. Te enviaremos actualizaciones por correo electrónico a medida que preparemos tu pedido.</p>
                </div>
            </section>

            @if ($paymentInstructions)
                <section class="checkout-section">
                    <h2 class="checkout-section__title">{{ $paymentInstructions['title'] }}</h2>
                    <div class="checkout-section__body">
                        <ul class="checkout-payment__instructions">
                            @foreach ($paymentInstructions['lines'] as $line)<li>{{ $line }}</li>@endforeach
                        </ul>
                        @if ($paymentInstructions['extra'])<p class="form-hint">{{ $paymentInstructions['extra'] }}</p>@endif
                        @if ($order->paymentMethod() === 'transfer')
                            <form method="POST" action="{{ $receiptUploadUrl }}" enctype="multipart/form-data">
                                @csrf
                                <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
                                <div class="form-group">
                                    <label class="form-label" for="success_receipt">Comprobante de transferencia <span class="form-label__required" aria-hidden="true">*</span></label>
                                    <input type="file" id="success_receipt" name="receipt" accept=".jpg,.jpeg,.png,.webp,.pdf" required class="form-input @error('receipt') form-input--error @enderror" @error('receipt') aria-invalid="true" aria-describedby="success-receipt-error" @enderror>
                                    <span class="form-hint" id="success-receipt-help">JPG, PNG, WEBP o PDF, máximo 5 MB.</span>
                                    @error('receipt')<span class="form-error" id="success-receipt-error">{{ $message }}</span>@enderror
                                </div>
                                <button type="submit" class="btn btn--ghost">Enviar comprobante</button>
                            </form>
                        @endif
                    </div>
                </section>
            @endif

            @if ($order->paymentReceipts->isNotEmpty())
                <section class="checkout-section">
                    <h2 class="checkout-section__title">Comprobantes enviados</h2>
                    <div class="checkout-section__body">
                        <ul class="checkout-payment__instructions">
                            @foreach ($order->paymentReceipts as $receipt)
                                <li>{{ $receipt->original_name }} — {{ ['pending' => 'Pendiente', 'accepted' => 'Aceptado', 'rejected' => 'Rechazado'][$receipt->status] ?? $receipt->status }}</li>
                            @endforeach
                        </ul>
                    </div>
                </section>
            @endif

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

            <div class="cart-summary__trust">
                <span class="cart-summary__trust-item">Recibirás confirmación por correo</span>
                <span class="cart-summary__trust-item">Embalaje cuidadoso para piezas vintage</span>
            </div>
        </aside>
    </div>
</div>
@endsection
