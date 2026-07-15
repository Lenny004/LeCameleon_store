@extends('layouts.store')

@section('title', 'Checkout — Le Cameleon')

@section('content')
{{--
  Single-step checkout. Field names must match CheckoutRequest:
  billing_address.*, shipping_address.*, coupon_code, notes.
--}}
<div class="container checkout" x-data="shippingQuote({
    calculateUrl: @js($quoteCalculateUrl ?? route('shipping.quote.calculate')),
    flatRate: {{ (float) ($shipping ?? 0) }},
})">
    <h1 class="heading-1" style="margin-bottom: var(--space-lg);">Checkout</h1>

    @if ($errors->any())
        <div class="flash flash--error" role="alert" style="margin-bottom: var(--space-lg);">
            <ul style="margin:0;padding-left:1.25rem;">
                @foreach ($errors->all() as $errorMessage)
                    <li>{{ $errorMessage }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="checkout-layout">
        <form method="POST" action="{{ route('checkout.store') }}" class="checkout-form">
            @csrf

            @guest
            <section class="checkout-section">
                <h2 class="checkout-section__title">Contacto</h2>
                <div class="form-group">
                    <label class="form-label" for="email">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        value="{{ old('email') }}"
                        required
                    >
                </div>
            </section>
            @endguest

            <section class="checkout-section">
                <h2 class="checkout-section__title">Dirección de envío</h2>

                <div class="form-row form-row--2">
                    <div class="form-group">
                        <label class="form-label" for="shipping_first_name">Nombre</label>
                        <input
                            type="text"
                            id="shipping_first_name"
                            name="shipping_address[first_name]"
                            class="form-input"
                            value="{{ old('shipping_address.first_name', auth()->user()->name ?? '') }}"
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="shipping_last_name">Apellido</label>
                        <input
                            type="text"
                            id="shipping_last_name"
                            name="shipping_address[last_name]"
                            class="form-input"
                            value="{{ old('shipping_address.last_name') }}"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="shipping_line1">Dirección</label>
                    <input
                        type="text"
                        id="shipping_line1"
                        name="shipping_address[line1]"
                        class="form-input"
                        value="{{ old('shipping_address.line1') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="destination_municipality_id">Municipio de entrega</label>
                    <input type="hidden" name="destination_municipality_id" :value="municipalityId || ''">
                    <select
                        id="destination_municipality_id"
                        class="form-input"
                        x-model="municipalityId"
                        @change="fetchQuote()"
                    >
                        <option value="">Selecciona un municipio para cotizar envío…</option>
                        @isset($departments)
                            @foreach ($departments as $department)
                                <optgroup label="{{ $department->name }}">
                                    @foreach ($department->municipalities as $municipality)
                                        <option
                                            value="{{ $municipality->id }}"
                                            @selected((string) old('destination_municipality_id') === (string) $municipality->id)
                                        >
                                            {{ $municipality->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        @endisset
                    </select>
                    <p class="text-muted" style="font-size:0.875rem;margin-top:var(--space-xs);">
                        Sin municipio seleccionado se aplica la tarifa plana de envío.
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="shipping_line2">Referencia adicional (opcional)</label>
                    <input
                        type="text"
                        id="shipping_line2"
                        name="shipping_address[line2]"
                        class="form-input"
                        value="{{ old('shipping_address.line2') }}"
                    >
                </div>

                <div class="form-row form-row--2">
                    <div class="form-group">
                        <label class="form-label" for="shipping_city">Ciudad</label>
                        <input
                            type="text"
                            id="shipping_city"
                            name="shipping_address[city]"
                            class="form-input"
                            value="{{ old('shipping_address.city') }}"
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="shipping_state">Estado / provincia</label>
                        <input
                            type="text"
                            id="shipping_state"
                            name="shipping_address[state]"
                            class="form-input"
                            value="{{ old('shipping_address.state') }}"
                        >
                    </div>
                </div>

                <div class="form-row form-row--2">
                    <div class="form-group">
                        <label class="form-label" for="shipping_postal_code">Código postal</label>
                        <input
                            type="text"
                            id="shipping_postal_code"
                            name="shipping_address[postal_code]"
                            class="form-input"
                            value="{{ old('shipping_address.postal_code') }}"
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="shipping_country">País (ISO-2)</label>
                        <input
                            type="text"
                            id="shipping_country"
                            name="shipping_address[country]"
                            class="form-input"
                            maxlength="2"
                            value="{{ old('shipping_address.country', 'SV') }}"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="shipping_phone">Teléfono</label>
                    <input
                        type="tel"
                        id="shipping_phone"
                        name="shipping_address[phone]"
                        class="form-input"
                        value="{{ old('shipping_address.phone') }}"
                    >
                </div>
            </section>

            <section class="checkout-section">
                <h2 class="checkout-section__title">Facturación</h2>
                <label class="form-checkbox" style="margin-bottom: var(--space-md);">
                    <input
                        type="checkbox"
                        name="same_as_shipping"
                        value="1"
                        checked
                        x-data
                        x-on:change="
                            const billingFields = document.querySelectorAll('[data-billing-field]');
                            billingFields.forEach((field) => {
                                field.closest('.form-group').style.display = $event.target.checked ? 'none' : '';
                            });
                        "
                    >
                    Usar la misma dirección para facturación
                </label>

                {{-- Mirrored into billing on submit when checkbox is checked (see app.js checkout helper) --}}
                <div id="billing-fields" data-billing-block>
                    <div class="form-row form-row--2">
                        <div class="form-group">
                            <label class="form-label" for="billing_first_name">Nombre</label>
                            <input data-billing-field type="text" id="billing_first_name" name="billing_address[first_name]" class="form-input" value="{{ old('billing_address.first_name') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="billing_last_name">Apellido</label>
                            <input data-billing-field type="text" id="billing_last_name" name="billing_address[last_name]" class="form-input" value="{{ old('billing_address.last_name') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="billing_line1">Dirección</label>
                        <input data-billing-field type="text" id="billing_line1" name="billing_address[line1]" class="form-input" value="{{ old('billing_address.line1') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="billing_line2">Línea 2</label>
                        <input data-billing-field type="text" id="billing_line2" name="billing_address[line2]" class="form-input" value="{{ old('billing_address.line2') }}">
                    </div>
                    <div class="form-row form-row--2">
                        <div class="form-group">
                            <label class="form-label" for="billing_city">Ciudad</label>
                            <input data-billing-field type="text" id="billing_city" name="billing_address[city]" class="form-input" value="{{ old('billing_address.city') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="billing_state">Estado</label>
                            <input data-billing-field type="text" id="billing_state" name="billing_address[state]" class="form-input" value="{{ old('billing_address.state') }}">
                        </div>
                    </div>
                    <div class="form-row form-row--2">
                        <div class="form-group">
                            <label class="form-label" for="billing_postal_code">Código postal</label>
                            <input data-billing-field type="text" id="billing_postal_code" name="billing_address[postal_code]" class="form-input" value="{{ old('billing_address.postal_code') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="billing_country">País (ISO-2)</label>
                            <input data-billing-field type="text" id="billing_country" name="billing_address[country]" class="form-input" maxlength="2" value="{{ old('billing_address.country', 'SV') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="billing_phone">Teléfono</label>
                        <input data-billing-field type="tel" id="billing_phone" name="billing_address[phone]" class="form-input" value="{{ old('billing_address.phone') }}">
                    </div>
                </div>
            </section>

            <section class="checkout-section">
                <h2 class="checkout-section__title">Método de pago</h2>
                <div class="form-group">
                    <label class="form-label">
                        <input type="radio" name="payment_method" value="manual" {{ old('payment_method', 'manual') === 'manual' ? 'checked' : '' }}>
                        Pago manual (transferencia / efectivo)
                    </label>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <input type="radio" name="payment_method" value="stripe" {{ old('payment_method') === 'stripe' ? 'checked' : '' }}>
                        Tarjeta (Stripe)
                    </label>
                </div>
            </section>

            <section class="checkout-section">
                <h2 class="checkout-section__title">Cupón y notas</h2>
                <div class="form-group">
                    <label class="form-label" for="coupon_code">Código de cupón</label>
                    <input type="text" id="coupon_code" name="coupon_code" class="form-input" value="{{ old('coupon_code') }}" placeholder="Opcional">
                </div>
                <div class="form-group">
                    <label class="form-label" for="notes">Notas del pedido</label>
                    <textarea id="notes" name="notes" class="form-textarea" rows="3">{{ old('notes') }}</textarea>
                </div>
            </section>

            <button type="submit" class="btn btn--primary btn--lg">Confirmar pedido</button>
        </form>

        <aside class="cart-summary">
            <h2 class="card__title" style="margin-bottom:var(--space-md);">Resumen</h2>
            @php
                $orderSubtotal = (float) ($subtotal ?? 0);
                $orderShipping = (float) ($shipping ?? 0);
                $orderTotal = $orderSubtotal + $orderShipping;
            @endphp
            <div class="cart-summary__row">
                <span>Subtotal</span>
                <span>${{ number_format($orderSubtotal, 2) }}</span>
            </div>
            <div class="cart-summary__row">
                <span>Envío</span>
                <span x-text="formatMoney(displayFee)"></span>
            </div>
            <div class="cart-summary__row cart-summary__row--total">
                <span>Total</span>
                <span x-text="formatMoney({{ $orderSubtotal }} + displayFee)"></span>
            </div>

            <div class="checkout-shipping-quote" x-show="municipalityId" x-cloak>
                <template x-if="loading">
                    <p class="text-muted" style="margin:0;">Calculando envío…</p>
                </template>
                <template x-if="quote && !loading">
                    <div>
                        <p style="margin:0;">
                            Entrega estimada: <strong x-text="formatEta(quote.eta_hours)"></strong>
                        </p>
                        <p class="checkout-shipping-quote__eta">
                            Próximo despacho: <span x-text="formatDispatch(quote.next_dispatch_at)"></span>
                        </p>
                        <template x-if="quote.warnings && quote.warnings.length">
                            <ul class="shipping-warnings" style="margin-top:var(--space-sm);">
                                <template x-for="warning in quote.warnings" :key="warning.id">
                                    <li
                                        class="shipping-warning"
                                        :class="'shipping-warning--' + (warning.severity || 'info')"
                                    >
                                        <strong class="shipping-warning__title" x-text="warning.title"></strong>
                                        <span class="shipping-warning__message" x-text="warning.message"></span>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>
                </template>
                <template x-if="error && !loading">
                    <p class="form-error" style="margin:0;" x-text="error"></p>
                </template>
            </div>

            @isset($cart)
                <ul style="list-style:none;padding:0;margin-top:var(--space-lg);">
                    @foreach ($cart->items as $cartItem)
                        <li style="display:flex;justify-content:space-between;gap:var(--space-sm);margin-bottom:var(--space-sm);font-size:0.9rem;">
                            <span>{{ $cartItem->product?->name }} × {{ $cartItem->quantity }}</span>
                            <span>${{ number_format((float) $cartItem->unit_price * $cartItem->quantity, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endisset
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Re-fetch shipping quote when returning with validation errors and a prior municipality selection.
    document.addEventListener('DOMContentLoaded', () => {
        const checkoutRoot = document.querySelector('.checkout[x-data]');
        if (!checkoutRoot || !window.Alpine) {
            return;
        }

        const component = Alpine.$data(checkoutRoot);
        const selectedMunicipality = @json(old('destination_municipality_id'));

        if (selectedMunicipality) {
            component.municipalityId = String(selectedMunicipality);
            component.fetchQuote();
        }
    });

    // Copy shipping address into billing when "same as shipping" is checked.
    document.querySelector('.checkout-form')?.addEventListener('submit', function (event) {
        const form = event.currentTarget;
        const useSameAddress = form.querySelector('[name="same_as_shipping"]')?.checked;

        if (!useSameAddress) {
            return;
        }

        const fieldNames = [
            'first_name', 'last_name', 'line1', 'line2',
            'city', 'state', 'postal_code', 'country', 'phone',
        ];

        fieldNames.forEach((fieldName) => {
            const shippingInput = form.querySelector(`[name="shipping_address[${fieldName}]"]`);
            const billingInput = form.querySelector(`[name="billing_address[${fieldName}]"]`);

            if (shippingInput && billingInput) {
                billingInput.value = shippingInput.value;
            }
        });
    });

    // Hide billing fields by default (same-as-shipping is checked).
    document.querySelectorAll('[data-billing-field]').forEach((field) => {
        const group = field.closest('.form-group');
        if (group) {
            group.style.display = 'none';
        }
    });
</script>
@endpush
