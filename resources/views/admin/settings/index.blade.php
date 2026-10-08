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

    <p class="text-muted">Estos valores provienen de la tabla de configuración y se usan en la tienda y la página de devoluciones. La moneda, los impuestos y el cálculo del envío siguen configurados en <code>config/store.php</code> y en las variables de entorno.</p>

    <button type="submit" class="btn btn--primary admin-form__submit">Guardar configuración</button>
</form>
@endsection
