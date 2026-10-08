@extends('layouts.admin')

@section('title', 'Configuración')
@section('page-title', 'Configuración')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="admin-form admin-form--medium">
    @csrf
    @method('PUT')

    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>

    <div class="card">
        <h2 class="card__title admin-form__title">Tienda</h2>
        <div class="admin-form__fields">
            <div class="form-group">
                <label class="form-label" for="store_name">Nombre de la tienda <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="text" id="store_name" name="store_name" class="form-input @error('store_name') form-input--error @enderror" value="{{ old('store_name', $settings['store_name']) }}" placeholder="Le Cameleon" maxlength="100" required autocomplete="organization" @error('store_name') aria-invalid="true" aria-describedby="store_name-error" @enderror>
                @error('store_name')<span class="form-error" id="store_name-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="contact_email">Correo de contacto</label>
                <input type="email" id="contact_email" name="contact_email" class="form-input @error('contact_email') form-input--error @enderror" value="{{ old('contact_email', $settings['contact_email']) }}" placeholder="contacto@tutienda.com" maxlength="255" inputmode="email" autocomplete="email" @error('contact_email') aria-invalid="true" aria-describedby="contact_email-error" @enderror>
                @error('contact_email')<span class="form-error" id="contact_email-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="contact_phone">Teléfono de contacto</label>
                <input type="tel" id="contact_phone" name="contact_phone" class="form-input @error('contact_phone') form-input--error @enderror" value="{{ old('contact_phone', $settings['contact_phone']) }}" placeholder="7777-7777" maxlength="50" inputmode="tel" autocomplete="tel" @error('contact_phone') aria-invalid="true" aria-describedby="contact_phone-error" @enderror>
                @error('contact_phone')<span class="form-error" id="contact_phone-error">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card__title admin-form__title">Devoluciones</h2>
        <div class="form-group">
            <label class="form-label" for="returns_policy">Política de devoluciones</label>
            <textarea id="returns_policy" name="returns_policy" class="form-textarea @error('returns_policy') form-textarea--error @enderror" rows="10" maxlength="10000" placeholder="Explica las condiciones y el proceso de devolución." @error('returns_policy') aria-invalid="true" aria-describedby="returns_policy-error" @enderror>{{ old('returns_policy', $settings['returns_policy']) }}</textarea>
            @error('returns_policy')<span class="form-error" id="returns_policy-error">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="card">
        <h2 class="card__title admin-form__title">Pagos</h2>
        <h3 class="admin-form__title admin-form__title--small">Transferencia bancaria</h3>
        <label class="form-checkbox"><input type="checkbox" class="form-checkbox__input" name="transfer[enabled]" value="1" @checked(old('transfer.enabled', $transfer['enabled']))> Habilitada</label>
        <div class="admin-form__fields">
            <div class="form-group"><label class="form-label" for="transfer_bank">Banco</label><input type="text" id="transfer_bank" name="transfer[bank]" value="{{ old('transfer.bank', $transfer['bank']) }}" placeholder="Banco Agrícola" maxlength="150" class="form-input"></div>
            <div class="form-group"><label class="form-label" for="transfer_account_holder">Titular</label><input type="text" id="transfer_account_holder" name="transfer[account_holder]" value="{{ old('transfer.account_holder', $transfer['account_holder']) }}" placeholder="Le Cameleon, S. A." maxlength="150" class="form-input"></div>
            <div class="form-group"><label class="form-label" for="transfer_account_number">Número de cuenta</label><input type="text" id="transfer_account_number" name="transfer[account_number]" value="{{ old('transfer.account_number', $transfer['account_number']) }}" placeholder="0000-0000-0000" maxlength="100" class="form-input"></div>
            <div class="form-group"><label class="form-label" for="transfer_account_type">Tipo de cuenta</label><select id="transfer_account_type" name="transfer[account_type]" class="form-select"><option value="savings" @selected(old('transfer.account_type', $transfer['account_type']) === 'savings')>Ahorro</option><option value="checking" @selected(old('transfer.account_type', $transfer['account_type']) === 'checking')>Corriente</option></select></div>
            <div class="form-group"><label class="form-label" for="transfer_instructions">Instrucciones adicionales</label><textarea id="transfer_instructions" name="transfer[instructions]" maxlength="1000" rows="4" placeholder="Envía el comprobante con tu número de pedido." class="form-textarea">{{ old('transfer.instructions', $transfer['instructions']) }}</textarea></div>
        </div>
        <h3 class="admin-form__title admin-form__title--small">Pago contra entrega</h3>
        <label class="form-checkbox"><input type="checkbox" class="form-checkbox__input" name="cod[enabled]" value="1" @checked(old('cod.enabled', $cod['enabled']))> Habilitado</label>
        <div class="form-group"><label class="form-label" for="cod_max_amount">Monto máximo opcional</label><input type="number" id="cod_max_amount" name="cod[max_amount]" value="{{ old('cod.max_amount', $cod['max_amount']) }}" placeholder="250.00" min="0" max="9999999999.99" step="0.01" inputmode="decimal" class="form-input"></div>
        <fieldset class="form-group"><legend class="form-label">Zonas permitidas</legend><p class="form-hint">Si no seleccionas ninguna, se permitirá en todos los municipios.</p>@foreach ($shippingZones as $zone)<label class="form-checkbox"><input type="checkbox" class="form-checkbox__input" name="cod[zone_ids][]" value="{{ $zone->id }}" @checked(in_array($zone->id, old('cod.zone_ids', $cod['zone_ids'] ?? []), true))>{{ $zone->name }}{{ $zone->municipality ? ' · '.$zone->municipality->name : '' }}</label>@endforeach</fieldset>
        <div class="form-group"><label class="form-label" for="cod_note">Nota para el cliente</label><textarea id="cod_note" name="cod[note]" maxlength="1000" rows="4" placeholder="Ten el monto exacto listo al recibir tu pedido." class="form-textarea">{{ old('cod.note', $cod['note']) }}</textarea></div>
    </div>

    <p class="text-muted">Estos valores provienen de la tabla de configuración y se usan en la tienda y la página de devoluciones. La moneda, los impuestos y el cálculo del envío siguen configurados en <code>config/store.php</code> y en las variables de entorno.</p>

    <button type="submit" class="btn btn--primary admin-form__submit">Guardar configuración</button>
</form>
@endsection
