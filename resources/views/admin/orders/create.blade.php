@extends('layouts.admin')

@section('title', 'Nuevo pedido')
@section('page-title', 'Nuevo pedido manual')

@section('content')
<form method="POST" action="{{ route('admin.orders.store-manual') }}" class="admin-form admin-form--medium">
    @csrf
    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>

    <section class="card">
        <h2 class="card__title">Cliente</h2>
        <div class="form-group">
            <label class="form-label" for="email">Correo <span class="form-label__required" aria-hidden="true">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="tu@correo.com" maxlength="255" inputmode="email" autocomplete="email" class="form-input @error('email') form-input--error @enderror" required @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email')<span class="form-error" id="email-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="customer_name">Nombre del cliente <span class="form-label__required" aria-hidden="true">*</span></label>
            <input id="customer_name" type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="María López" maxlength="255" autocomplete="name" class="form-input @error('customer_name') form-input--error @enderror" required @error('customer_name') aria-invalid="true" aria-describedby="customer-name-error" @enderror>
            @error('customer_name')<span class="form-error" id="customer-name-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="phone">Teléfono</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="7777-7777" maxlength="30" inputmode="tel" autocomplete="tel" class="form-input @error('phone') form-input--error @enderror" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
            @error('phone')<span class="form-error" id="phone-error">{{ $message }}</span>@enderror
        </div>
    </section>

    <section class="card">
        <h2 class="card__title">Productos</h2>
        @error('items')<span class="form-error" id="items-error">{{ $message }}</span>@enderror
        @foreach (range(0, 4) as $index)
            <div class="form-row form-row--cols-2">
                <div class="form-group">
                    <label class="form-label" for="item_{{ $index }}_product_id">Producto {{ $index + 1 }}</label>
                    <select id="item_{{ $index }}_product_id" name="items[{{ $index }}][product_id]" class="form-select @error('items.'.$index.'.product_id') form-select--error @enderror" @error('items.'.$index.'.product_id') aria-invalid="true" aria-describedby="item_{{ $index }}_product_id-error" @enderror>
                        <option value="">Selecciona un producto</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('items.'.$index.'.product_id') === $product->id)>{{ $product->name }} ({{ $product->sku }}) · ${{ number_format((float) $product->price, 2) }}</option>
                        @endforeach
                    </select>
                    @error('items.'.$index.'.product_id')<span class="form-error" id="item_{{ $index }}_product_id-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="item_{{ $index }}_quantity">Cantidad {{ $index + 1 }}</label>
                    <input id="item_{{ $index }}_quantity" type="number" name="items[{{ $index }}][quantity]" value="{{ old('items.'.$index.'.quantity') }}" placeholder="1" min="1" max="32767" step="1" inputmode="numeric" class="form-input @error('items.'.$index.'.quantity') form-input--error @enderror" @error('items.'.$index.'.quantity') aria-invalid="true" aria-describedby="item_{{ $index }}_quantity-error" @enderror>
                    @error('items.'.$index.'.quantity')<span class="form-error" id="item_{{ $index }}_quantity-error">{{ $message }}</span>@enderror
                </div>
            </div>
        @endforeach
    </section>

    <section class="card">
        <h2 class="card__title">Envío</h2>
        <div class="form-row form-row--cols-2">
            <div class="form-group">
                <label class="form-label" for="manual_first_name">Nombre de entrega <span class="form-label__required" aria-hidden="true">*</span></label>
                <input id="manual_first_name" type="text" name="shipping_address[first_name]" value="{{ old('shipping_address.first_name', old('customer_name')) }}" placeholder="María" maxlength="100" autocomplete="given-name" class="form-input @error('shipping_address.first_name') form-input--error @enderror" required @error('shipping_address.first_name') aria-invalid="true" aria-describedby="manual-first-name-error" @enderror>
                @error('shipping_address.first_name')<span class="form-error" id="manual-first-name-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="manual_last_name">Apellido <span class="form-label__required" aria-hidden="true">*</span></label>
                <input id="manual_last_name" type="text" name="shipping_address[last_name]" value="{{ old('shipping_address.last_name') }}" placeholder="López" maxlength="100" autocomplete="family-name" class="form-input @error('shipping_address.last_name') form-input--error @enderror" required @error('shipping_address.last_name') aria-invalid="true" aria-describedby="manual-last-name-error" @enderror>
                @error('shipping_address.last_name')<span class="form-error" id="manual-last-name-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="manual_line1">Dirección <span class="form-label__required" aria-hidden="true">*</span></label>
            <input id="manual_line1" type="text" name="shipping_address[line1]" value="{{ old('shipping_address.line1') }}" placeholder="Calle Principal 123" maxlength="255" autocomplete="address-line1" class="form-input @error('shipping_address.line1') form-input--error @enderror" required @error('shipping_address.line1') aria-invalid="true" aria-describedby="manual-line1-error" @enderror>
            @error('shipping_address.line1')<span class="form-error" id="manual-line1-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="manual_line2">Referencia adicional</label>
            <input id="manual_line2" type="text" name="shipping_address[line2]" value="{{ old('shipping_address.line2') }}" placeholder="Apartamento o punto cercano" maxlength="255" autocomplete="address-line2" class="form-input @error('shipping_address.line2') form-input--error @enderror" @error('shipping_address.line2') aria-invalid="true" aria-describedby="manual-line2-error" @enderror>
            @error('shipping_address.line2')<span class="form-error" id="manual-line2-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-row form-row--cols-2">
            <div class="form-group">
                <label class="form-label" for="manual_city">Ciudad <span class="form-label__required" aria-hidden="true">*</span></label>
                <input id="manual_city" type="text" name="shipping_address[city]" value="{{ old('shipping_address.city') }}" placeholder="San Salvador" maxlength="100" autocomplete="address-level2" class="form-input @error('shipping_address.city') form-input--error @enderror" required @error('shipping_address.city') aria-invalid="true" aria-describedby="manual-city-error" @enderror>
                @error('shipping_address.city')<span class="form-error" id="manual-city-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="manual_state">Departamento</label>
                <input id="manual_state" type="text" name="shipping_address[state]" value="{{ old('shipping_address.state') }}" placeholder="San Salvador" maxlength="100" autocomplete="address-level1" class="form-input @error('shipping_address.state') form-input--error @enderror" @error('shipping_address.state') aria-invalid="true" aria-describedby="manual-state-error" @enderror>
                @error('shipping_address.state')<span class="form-error" id="manual-state-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="form-row form-row--cols-2">
            <div class="form-group">
                <label class="form-label" for="manual_postal_code">Código postal <span class="form-label__required" aria-hidden="true">*</span></label>
                <input id="manual_postal_code" type="text" name="shipping_address[postal_code]" value="{{ old('shipping_address.postal_code') }}" placeholder="1101" maxlength="20" inputmode="numeric" autocomplete="postal-code" class="form-input @error('shipping_address.postal_code') form-input--error @enderror" required @error('shipping_address.postal_code') aria-invalid="true" aria-describedby="manual-postal-error" @enderror>
                @error('shipping_address.postal_code')<span class="form-error" id="manual-postal-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="manual_phone">Teléfono</label>
                <input id="manual_phone" type="tel" name="shipping_address[phone]" value="{{ old('shipping_address.phone', old('phone')) }}" placeholder="7777-7777" maxlength="30" inputmode="tel" autocomplete="tel" class="form-input @error('shipping_address.phone') form-input--error @enderror" @error('shipping_address.phone') aria-invalid="true" aria-describedby="manual-phone-error" @enderror>
                @error('shipping_address.phone')<span class="form-error" id="manual-phone-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="manual_country">País <span class="form-label__required" aria-hidden="true">*</span></label>
            <input id="manual_country" type="text" name="shipping_address[country]" value="{{ old('shipping_address.country', 'SV') }}" placeholder="SV" maxlength="2" autocomplete="country" class="form-input @error('shipping_address.country') form-input--error @enderror" required @error('shipping_address.country') aria-invalid="true" aria-describedby="manual-country-error" @enderror>
            @error('shipping_address.country')<span class="form-error" id="manual-country-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="destination_municipality_id">Municipio de destino</label>
            @include('components.municipality-select', ['departments' => $departments, 'name' => 'destination_municipality_id', 'id' => 'destination_municipality_id', 'selected' => old('destination_municipality_id')])
            @error('destination_municipality_id')<span class="form-error" id="destination_municipality_id-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="shipping_override">Costo de envío manual</label>
            <input id="shipping_override" type="number" name="shipping_override" value="{{ old('shipping_override') }}" placeholder="0.00" min="0" max="9999999999.99" step="0.01" inputmode="decimal" class="form-input @error('shipping_override') form-input--error @enderror" @error('shipping_override') aria-invalid="true" aria-describedby="shipping-override-error" @enderror>
            @error('shipping_override')<span class="form-error" id="shipping-override-error">{{ $message }}</span>@enderror
        </div>
    </section>

    <section class="card">
        <h2 class="card__title">Pago</h2>
        <div class="form-group">
            <label class="form-label" for="payment_method">Método de pago <span class="form-label__required" aria-hidden="true">*</span></label>
            <select id="payment_method" name="payment_method" class="form-select @error('payment_method') form-select--error @enderror" required @error('payment_method') aria-invalid="true" aria-describedby="payment-method-error" @enderror>
                <option value="manual" @selected(old('payment_method', 'manual') === 'manual')>Pago manual</option>
                <option value="transfer" @selected(old('payment_method') === 'transfer')>Transferencia bancaria</option>
                <option value="cod" @selected(old('payment_method') === 'cod')>Pago contra entrega</option>
            </select>
            @error('payment_method')<span class="form-error" id="payment-method-error">{{ $message }}</span>@enderror
        </div>
        <label class="form-checkbox"><input type="checkbox" class="form-checkbox__input" name="mark_paid" value="1" @checked(old('mark_paid'))> Marcar como pagado</label>
        <label class="form-checkbox"><input type="checkbox" class="form-checkbox__input" name="send_email" value="1" @checked(old('send_email', true))> Enviar correo al cliente</label>
        <div class="form-group">
            <label class="form-label" for="manual_notes">Notas</label>
            <textarea id="manual_notes" name="notes" maxlength="1000" placeholder="Indicaciones internas del pedido." class="form-textarea @error('notes') form-textarea--error @enderror" @error('notes') aria-invalid="true" aria-describedby="manual-notes-error" @enderror>{{ old('notes') }}</textarea>
            @error('notes')<span class="form-error" id="manual-notes-error">{{ $message }}</span>@enderror
        </div>
    </section>

    <button type="submit" class="btn btn--primary">Crear pedido</button>
</form>
@endsection
