@extends('layouts.store')

@section('title', 'Comprobante del pedido '.$order->number)

@section('content')
<div class="container checkout">
    <div class="checkout-form">
        <section class="checkout-section">
            <h1 class="checkout-section__title">Comprobante del pedido #{{ $order->number }}</h1>
            <div class="checkout-section__body">
                <p>Sube el comprobante de tu transferencia para que podamos revisar el pago.</p>
                @if ($paymentInstructions)
                    <h2 class="text-small">{{ $paymentInstructions['title'] }}</h2>
                    <ul class="checkout-payment__instructions">
                        @foreach ($paymentInstructions['lines'] as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                    @if ($paymentInstructions['extra'])
                        <p class="form-hint">{{ $paymentInstructions['extra'] }}</p>
                    @endif
                @endif
            </div>
        </section>

        <section class="checkout-section">
            <div class="checkout-section__body">
                <form method="POST" action="{{ $receiptUploadUrl }}" enctype="multipart/form-data">
                    @csrf
                    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
                    <div class="form-group">
                        <label class="form-label" for="receipt">Comprobante de transferencia <span class="form-label__required" aria-hidden="true">*</span></label>
                        <input id="receipt" name="receipt" type="file" class="form-input @error('receipt') form-input--error @enderror" accept=".jpg,.jpeg,.png,.webp,.pdf" required @error('receipt') aria-invalid="true" aria-describedby="receipt-error" @enderror>
                        <span class="form-hint" id="receipt-help">JPG, PNG, WEBP o PDF, máximo 5 MB.</span>
                        @error('receipt')
                            <span class="form-error" id="receipt-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn--primary">Enviar comprobante</button>
                </form>
            </div>
        </section>

        @if ($order->paymentReceipts->isNotEmpty())
            <section class="checkout-section">
                <div class="checkout-section__body">
                    <h2 class="text-small">Comprobantes enviados</h2>
                    <ul class="checkout-payment__instructions">
                        @foreach ($order->paymentReceipts as $receipt)
                            <li>{{ $receipt->original_name }} — {{ ['pending' => 'Pendiente', 'accepted' => 'Aceptado', 'rejected' => 'Rechazado'][$receipt->status] ?? $receipt->status }}</li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif
    </div>
</div>
@endsection
