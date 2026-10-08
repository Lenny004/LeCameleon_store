@extends('layouts.store')

@section('title', 'Finalizar compra — Le Cameleon')

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
        <h1 class="heading-2">Finalizar compra</h1>
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
            <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>

            @guest
            <section class="checkout-section">
                <h2 class="checkout-section__title">Contacto</h2>
                <div class="checkout-section__body">
                    <div class="form-group">
                        <label class="form-label" for="email">Correo electrónico <span class="form-label__required" aria-hidden="true">*</span></label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input @error('email') form-input--error @enderror"
                            value="{{ old('email') }}"
                            placeholder="tu@correo.com"
                            maxlength="255"
                            inputmode="email"
                            required
                            autocomplete="email"
                            @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        >
                        @error('email')
                            <span class="form-error" id="email-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </section>
            @endguest

            <section class="checkout-section">
                <h2 class="checkout-section__title">Dirección de envío</h2>
                <div class="checkout-section__body">
                    <div class="form-row form-row--cols-2">
                        <div class="form-group">
                            <label class="form-label" for="shipping_first_name">Nombre <span class="form-label__required" aria-hidden="true">*</span></label>
                            <input
                                type="text"
                                id="shipping_first_name"
                                name="shipping_address[first_name]"
                                class="form-input @error('shipping_address.first_name') form-input--error @enderror"
                                value="{{ old('shipping_address.first_name', auth()->user()->name ?? '') }}"
                                placeholder="María"
                                maxlength="100"
                                required
                                autocomplete="given-name"
                                @error('shipping_address.first_name') aria-invalid="true" aria-describedby="shipping_address_first_name-error" @enderror
                            >
                            @error('shipping_address.first_name')
                                <span class="form-error" id="shipping_address_first_name-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="shipping_last_name">Apellido <span class="form-label__required" aria-hidden="true">*</span></label>
                            <input
                                type="text"
                                id="shipping_last_name"
                                name="shipping_address[last_name]"
                                class="form-input @error('shipping_address.last_name') form-input--error @enderror"
                                value="{{ old('shipping_address.last_name') }}"
                                placeholder="López"
                                maxlength="100"
                                required
                                autocomplete="family-name"
                                @error('shipping_address.last_name') aria-invalid="true" aria-describedby="shipping_address_last_name-error" @enderror
                            >
                            @error('shipping_address.last_name')<span class="form-error" id="shipping_address_last_name-error">{{ $message }}</span>@enderror
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="form-label" for="shipping_line1">Dirección <span class="form-label__required" aria-hidden="true">*</span></label>
                        <input
                            type="text"
                            id="shipping_line1"
                            name="shipping_address[line1]"
                            class="form-input @error('shipping_address.line1') form-input--error @enderror"
                            value="{{ old('shipping_address.line1') }}"
                            placeholder="Calle Principal 123"
                            maxlength="255"
                            required
                            autocomplete="address-line1"
                            @error('shipping_address.line1') aria-invalid="true" aria-describedby="shipping_address_line1-error" @enderror
                        >
                        @error('shipping_address.line1')<span class="form-error" id="shipping_address_line1-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="destination_municipality_id">Municipio de entrega</label>
                        <input type="hidden" name="destination_municipality_id" :value="municipalityId || ''">
                        <select
                            id="destination_municipality_id"
                            class="form-select @error('destination_municipality_id') form-select--error @enderror"
                            @error('destination_municipality_id') aria-invalid="true" aria-describedby="destination_municipality_id-error" @enderror
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
                        @error('destination_municipality_id')<span class="form-error" id="destination_municipality_id-error">{{ $message }}</span>@enderror
                        <p class="form-hint">Sin municipio seleccionado se aplica la tarifa plana de envío.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="shipping_line2">Referencia adicional (opcional)</label>
                        <input
                            type="text"
                            id="shipping_line2"
                            name="shipping_address[line2]"
                            class="form-input @error('shipping_address.line2') form-input--error @enderror"
                            value="{{ old('shipping_address.line2') }}"
                            placeholder="Apartamento, referencia o punto cercano"
                            maxlength="255"
                            autocomplete="address-line2"
                            @error('shipping_address.line2') aria-invalid="true" aria-describedby="shipping_address_line2-error" @enderror
                        >
                        @error('shipping_address.line2')<span class="form-error" id="shipping_address_line2-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-row form-row--cols-2">
                        <div class="form-group">
                            <label class="form-label" for="shipping_city">Ciudad <span class="form-label__required" aria-hidden="true">*</span></label>
                            <input
                                type="text"
                                id="shipping_city"
                                name="shipping_address[city]"
                                class="form-input @error('shipping_address.city') form-input--error @enderror"
                                value="{{ old('shipping_address.city') }}"
                                placeholder="San Salvador"
                                maxlength="100"
                                required
                                autocomplete="address-level2"
                                @error('shipping_address.city') aria-invalid="true" aria-describedby="shipping_address_city-error" @enderror
                            >
                            @error('shipping_address.city')<span class="form-error" id="shipping_address_city-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="shipping_state">Estado / provincia</label>
                            <input
                                type="text"
                                id="shipping_state"
                                name="shipping_address[state]"
                                class="form-input @error('shipping_address.state') form-input--error @enderror"
                                value="{{ old('shipping_address.state') }}"
                                placeholder="San Salvador"
                                maxlength="100"
                                autocomplete="address-level1"
                                @error('shipping_address.state') aria-invalid="true" aria-describedby="shipping_address_state-error" @enderror
                            >
                            @error('shipping_address.state')<span class="form-error" id="shipping_address_state-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-row form-row--cols-2">
                        <div class="form-group">
                            <label class="form-label" for="shipping_postal_code">Código postal <span class="form-label__required" aria-hidden="true">*</span></label>
                            <input
                                type="text"
                                id="shipping_postal_code"
                                name="shipping_address[postal_code]"
                                class="form-input @error('shipping_address.postal_code') form-input--error @enderror"
                                value="{{ old('shipping_address.postal_code') }}"
                                placeholder="1101"
                                maxlength="20"
                                inputmode="numeric"
                                required
                                autocomplete="postal-code"
                                @error('shipping_address.postal_code') aria-invalid="true" aria-describedby="shipping_address_postal_code-error" @enderror
                            >
                            @error('shipping_address.postal_code')<span class="form-error" id="shipping_address_postal_code-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="shipping_country">País (ISO-2) <span class="form-label__required" aria-hidden="true">*</span></label>
                            <input
                                type="text"
                                id="shipping_country"
                                name="shipping_address[country]"
                                class="form-input @error('shipping_address.country') form-input--error @enderror"
                                maxlength="2"
                                value="{{ old('shipping_address.country', 'SV') }}"
                                placeholder="SV"
                                inputmode="text"
                                required
                                autocomplete="country"
                                @error('shipping_address.country') aria-invalid="true" aria-describedby="shipping_address_country-error" @enderror
                            >
                            @error('shipping_address.country')<span class="form-error" id="shipping_address_country-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="shipping_phone">Teléfono</label>
                        <input
                            type="tel"
                            id="shipping_phone"
                            name="shipping_address[phone]"
                            class="form-input @error('shipping_address.phone') form-input--error @enderror"
                            value="{{ old('shipping_address.phone') }}"
                            placeholder="7777-7777"
                            maxlength="30"
                            inputmode="tel"
                            autocomplete="tel"
                            @error('shipping_address.phone') aria-invalid="true" aria-describedby="shipping_address_phone-error" @enderror
                        >
                        @error('shipping_address.phone')<span class="form-error" id="shipping_address_phone-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </section>

            <section class="checkout-section" x-data="{ sameAsShipping: @js(session()->hasOldInput() ? (bool) old('same_as_shipping') : true) }">
                <h2 class="checkout-section__title">Facturación</h2>
                <div class="checkout-section__body">
                    <label class="form-checkbox checkout-billing-toggle">
                        <input
                            type="checkbox"
                            class="form-checkbox__input"
                            name="same_as_shipping"
                            value="1"
                            @checked(session()->hasOldInput() ? (bool) old('same_as_shipping') : true)
                            x-model="sameAsShipping"
                        >
                        Usar la misma dirección para facturación
                    </label>

                    <div id="billing-fields" class="checkout-billing__fields" data-billing-block x-show="!sameAsShipping" x-cloak>
                        <div class="form-row form-row--cols-2">
                            <div class="form-group">
                                <label class="form-label" for="billing_first_name">Nombre <span class="form-label__required" aria-hidden="true">*</span></label>
                                <input data-billing-field type="text" id="billing_first_name" name="billing_address[first_name]" class="form-input @error('billing_address.first_name') form-input--error @enderror" value="{{ old('billing_address.first_name') }}" placeholder="María" maxlength="100" autocomplete="given-name" x-bind:required="!sameAsShipping" @error('billing_address.first_name') aria-invalid="true" aria-describedby="billing_address_first_name-error" @enderror>
                                @error('billing_address.first_name')<span class="form-error" id="billing_address_first_name-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="billing_last_name">Apellido <span class="form-label__required" aria-hidden="true">*</span></label>
                                <input data-billing-field type="text" id="billing_last_name" name="billing_address[last_name]" class="form-input @error('billing_address.last_name') form-input--error @enderror" value="{{ old('billing_address.last_name') }}" placeholder="López" maxlength="100" autocomplete="family-name" x-bind:required="!sameAsShipping" @error('billing_address.last_name') aria-invalid="true" aria-describedby="billing_address_last_name-error" @enderror>
                                @error('billing_address.last_name')<span class="form-error" id="billing_address_last_name-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="billing_line1">Dirección <span class="form-label__required" aria-hidden="true">*</span></label>
                            <input data-billing-field type="text" id="billing_line1" name="billing_address[line1]" class="form-input @error('billing_address.line1') form-input--error @enderror" value="{{ old('billing_address.line1') }}" placeholder="Calle Principal 123" maxlength="255" autocomplete="address-line1" x-bind:required="!sameAsShipping" @error('billing_address.line1') aria-invalid="true" aria-describedby="billing_address_line1-error" @enderror>
                            @error('billing_address.line1')<span class="form-error" id="billing_address_line1-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="billing_line2">Línea 2</label>
                            <input data-billing-field type="text" id="billing_line2" name="billing_address[line2]" class="form-input @error('billing_address.line2') form-input--error @enderror" value="{{ old('billing_address.line2') }}" placeholder="Apartamento, referencia o punto cercano" maxlength="255" autocomplete="address-line2" @error('billing_address.line2') aria-invalid="true" aria-describedby="billing_address_line2-error" @enderror>
                            @error('billing_address.line2')<span class="form-error" id="billing_address_line2-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-row form-row--cols-2">
                            <div class="form-group">
                                <label class="form-label" for="billing_city">Ciudad <span class="form-label__required" aria-hidden="true">*</span></label>
                                <input data-billing-field type="text" id="billing_city" name="billing_address[city]" class="form-input @error('billing_address.city') form-input--error @enderror" value="{{ old('billing_address.city') }}" placeholder="San Salvador" maxlength="100" autocomplete="address-level2" x-bind:required="!sameAsShipping" @error('billing_address.city') aria-invalid="true" aria-describedby="billing_address_city-error" @enderror>
                                @error('billing_address.city')<span class="form-error" id="billing_address_city-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="billing_state">Estado</label>
                                <input data-billing-field type="text" id="billing_state" name="billing_address[state]" class="form-input @error('billing_address.state') form-input--error @enderror" value="{{ old('billing_address.state') }}" placeholder="San Salvador" maxlength="100" autocomplete="address-level1" @error('billing_address.state') aria-invalid="true" aria-describedby="billing_address_state-error" @enderror>
                                @error('billing_address.state')<span class="form-error" id="billing_address_state-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="form-row form-row--cols-2">
                            <div class="form-group">
                                <label class="form-label" for="billing_postal_code">Código postal <span class="form-label__required" aria-hidden="true">*</span></label>
                                <input data-billing-field type="text" id="billing_postal_code" name="billing_address[postal_code]" class="form-input @error('billing_address.postal_code') form-input--error @enderror" value="{{ old('billing_address.postal_code') }}" placeholder="1101" maxlength="20" inputmode="numeric" autocomplete="postal-code" x-bind:required="!sameAsShipping" @error('billing_address.postal_code') aria-invalid="true" aria-describedby="billing_address_postal_code-error" @enderror>
                                @error('billing_address.postal_code')<span class="form-error" id="billing_address_postal_code-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="billing_country">País (ISO-2) <span class="form-label__required" aria-hidden="true">*</span></label>
                                <input data-billing-field type="text" id="billing_country" name="billing_address[country]" class="form-input @error('billing_address.country') form-input--error @enderror" maxlength="2" value="{{ old('billing_address.country', 'SV') }}" placeholder="SV" inputmode="text" autocomplete="country" x-bind:required="!sameAsShipping" @error('billing_address.country') aria-invalid="true" aria-describedby="billing_address_country-error" @enderror>
                                @error('billing_address.country')<span class="form-error" id="billing_address_country-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="billing_phone">Teléfono</label>
                            <input data-billing-field type="tel" id="billing_phone" name="billing_address[phone]" class="form-input @error('billing_address.phone') form-input--error @enderror" value="{{ old('billing_address.phone') }}" placeholder="7777-7777" maxlength="30" inputmode="tel" autocomplete="tel" @error('billing_address.phone') aria-invalid="true" aria-describedby="billing_address_phone-error" @enderror>
                            @error('billing_address.phone')<span class="form-error" id="billing_address_phone-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="checkout-section">
                <h2 class="checkout-section__title">Método de pago</h2>
                <div class="checkout-section__body checkout-payment">
                    <label class="form-radio">
                        <input type="radio" class="form-radio__input" name="payment_method" value="manual" {{ old('payment_method', 'manual') === 'manual' ? 'checked' : '' }} @error('payment_method') aria-invalid="true" aria-describedby="payment_method-error" @enderror>
                        <span class="form-radio__label">
                            Pago manual
                            <span class="form-radio__hint">Transferencia bancaria o efectivo contra entrega</span>
                        </span>
                    </label>
                    <label class="form-radio">
                        <input type="radio" class="form-radio__input" name="payment_method" value="stripe" {{ old('payment_method') === 'stripe' ? 'checked' : '' }} @error('payment_method') aria-invalid="true" aria-describedby="payment_method-error" @enderror>
                        <span class="form-radio__label">
                            Tarjeta de crédito o débito
                            <span class="form-radio__hint">Procesado de forma segura con Stripe</span>
                        </span>
                    </label>
                    @error('payment_method')<span class="form-error" id="payment_method-error">{{ $message }}</span>@enderror
                </div>
            </section>

            <section class="checkout-section">
                <h2 class="checkout-section__title">Cupón y notas</h2>
                <div class="checkout-section__body">
                    <div class="form-group">
                        <label class="form-label" for="coupon_code">Código de cupón</label>
                        <input type="text" id="coupon_code" name="coupon_code" class="form-input @error('coupon_code') form-input--error @enderror" value="{{ old('coupon_code') }}" placeholder="Ej. BIENVENIDA10" maxlength="50" autocomplete="off" @error('coupon_code') aria-invalid="true" aria-describedby="coupon_code-error" @enderror>
                        @error('coupon_code')
                            <span class="form-error" id="coupon_code-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="notes">Notas del pedido</label>
                        <textarea id="notes" name="notes" class="form-textarea @error('notes') form-textarea--error @enderror" rows="3" maxlength="1000" placeholder="Ej. Entregar después de las 5 p. m." @error('notes') aria-invalid="true" aria-describedby="notes-error" @enderror>{{ old('notes') }}</textarea>
                        @error('notes')<span class="form-error" id="notes-error">{{ $message }}</span>@enderror
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
                            Entrega estimada: <strong class="checkout-shipping-quote__lead-value" x-text="formatEta(quote.eta_hours)"></strong>
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
<script nonce="{{ Vite::cspNonce() }}">
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
