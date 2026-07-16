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
    <header class="checkout__header">
        <h1 class="heading-2">Checkout</h1>
    </header>

    <nav class="checkout-steps" aria-label="Pasos del checkout">
        <span class="checkout-step checkout-step--active">
            <span class="checkout-step__num">1</span>
            Envío y pago
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

    @if ($errors->any())
        <div class="flash flash--error" role="alert">
            <ul>
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
                <div class="checkout-section__body">
                    <div class="form-group">
                        <label class="form-label" for="email">Correo electrónico</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input @error('email') form-input--error @enderror"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                        >
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>
            @endguest

            <section class="checkout-section">
                <h2 class="checkout-section__title">Dirección de envío</h2>
                <div class="checkout-section__body">
                    <div class="form-row form-row--2">
                        <div class="form-group">
                            <label class="form-label" for="shipping_first_name">Nombre</label>
                            <input
                                type="text"
                                id="shipping_first_name"
                                name="shipping_address[first_name]"
                                class="form-input @error('shipping_address.first_name') form-input--error @enderror"
                                value="{{ old('shipping_address.first_name', auth()->user()->name ?? '') }}"
                                required
                                autocomplete="given-name"
                            >
                            @error('shipping_address.first_name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
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
                                autocomplete="family-name"
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
                            autocomplete="address-line1"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="destination_municipality_id">Municipio de entrega</label>
                        <input type="hidden" name="destination_municipality_id" :value="municipalityId || ''">
                        <select
                            id="destination_municipality_id"
                            class="form-select"
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
                        <p class="form-hint">Sin municipio seleccionado se aplica la tarifa plana de envío.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="shipping_line2">Referencia adicional (opcional)</label>
                        <input
                            type="text"
                            id="shipping_line2"
                            name="shipping_address[line2]"
                            class="form-input"
                            value="{{ old('shipping_address.line2') }}"
                            autocomplete="address-line2"
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
                                autocomplete="address-level2"
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
                                autocomplete="address-level1"
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
                                autocomplete="postal-code"
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
                                autocomplete="country"
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
                            autocomplete="tel"
                        >
                    </div>
                </div>
            </section>

            <section class="checkout-section">
                <h2 class="checkout-section__title">Facturación</h2>
                <div class="checkout-section__body">
                    <label class="form-checkbox checkout-billing-toggle">
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
                </div>
            </section>

            <section class="checkout-section">
                <h2 class="checkout-section__title">Método de pago</h2>
                <div class="checkout-section__body checkout-payment">
                    <label class="form-radio">
                        <input type="radio" name="payment_method" value="manual" {{ old('payment_method', 'manual') === 'manual' ? 'checked' : '' }}>
                        <span class="form-radio__label">
                            Pago manual
                            <span class="form-radio__hint">Transferencia bancaria o efectivo contra entrega</span>
                        </span>
                    </label>
                    <label class="form-radio">
                        <input type="radio" name="payment_method" value="stripe" {{ old('payment_method') === 'stripe' ? 'checked' : '' }}>
                        <span class="form-radio__label">
                            Tarjeta de crédito o débito
                            <span class="form-radio__hint">Procesado de forma segura con Stripe</span>
                        </span>
                    </label>
                </div>
            </section>

            <section class="checkout-section">
                <h2 class="checkout-section__title">Cupón y notas</h2>
                <div class="checkout-section__body">
                    <div class="form-group">
                        <label class="form-label" for="coupon_code">Código de cupón</label>
                        <input type="text" id="coupon_code" name="coupon_code" class="form-input @error('coupon_code') form-input--error @enderror" value="{{ old('coupon_code') }}" placeholder="Opcional">
                        @error('coupon_code')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="notes">Notas del pedido</label>
                        <textarea id="notes" name="notes" class="form-textarea" rows="3" placeholder="Instrucciones especiales de entrega, horarios preferidos…">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </section>

            <div class="checkout-submit">
                <button type="submit" class="btn btn--primary btn--lg">Confirmar pedido</button>
                <p class="checkout-submit__note">Al confirmar, aceptas nuestros términos de compra. Te enviaremos un correo con los detalles del pedido.</p>
            </div>
        </form>

        <aside class="order-summary">
            <h2 class="order-summary__title">Resumen del pedido</h2>

            @isset($cart)
                <ul class="order-summary__items">
                    @foreach ($cart->items as $cartItem)
                        <li class="order-summary__item">
                            <span class="order-summary__item-name">{{ $cartItem->product?->name }} × {{ $cartItem->quantity }}</span>
                            <span class="order-summary__item-price">${{ number_format((float) $cartItem->unit_price * $cartItem->quantity, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endisset

            @php
                $orderSubtotal = (float) ($subtotal ?? 0);
                $orderShipping = (float) ($shipping ?? 0);
            @endphp
            <div class="cart-summary__rows">
                <div class="cart-summary__row">
                    <span class="cart-summary__row-label">Subtotal</span>
                    <span class="cart-summary__row-value">${{ number_format($orderSubtotal, 2) }}</span>
                </div>
                <div class="cart-summary__row">
                    <span class="cart-summary__row-label">Envío</span>
                    <span class="cart-summary__row-value" x-text="formatMoney(displayFee)"></span>
                </div>
                <div class="cart-summary__row cart-summary__row--total">
                    <span class="cart-summary__row-label">Total</span>
                    <span class="cart-summary__row-value" x-text="formatMoney({{ $orderSubtotal }} + displayFee)"></span>
                </div>
            </div>

            <div class="checkout-shipping-quote" x-show="municipalityId" x-cloak>
                <template x-if="loading">
                    <p class="form-hint checkout-shipping-quote__loading">Calculando envío…</p>
                </template>
                <template x-if="quote && !loading">
                    <div>
                        <p class="checkout-shipping-quote__lead">
                            Entrega estimada: <strong x-text="formatEta(quote.eta_hours)"></strong>
                        </p>
                        <p class="checkout-shipping-quote__eta">
                            Próximo despacho: <span x-text="formatDispatch(quote.next_dispatch_at)"></span>
                        </p>
                        <template x-if="quote.warnings && quote.warnings.length">
                            <ul class="shipping-warnings">
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
                    <p class="form-error checkout-shipping-quote__error" x-text="error"></p>
                </template>
            </div>

            <div class="cart-summary__trust">
                <span class="cart-summary__trust-item">Compra segura — datos protegidos</span>
                <span class="cart-summary__trust-item">Embalaje para piezas delicadas</span>
                <span class="cart-summary__trust-item">Descripciones honestas del estado</span>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

    document.querySelectorAll('[data-billing-field]').forEach((field) => {
        const group = field.closest('.form-group');
        if (group) {
            group.style.display = 'none';
        }
    });
</script>
@endpush
