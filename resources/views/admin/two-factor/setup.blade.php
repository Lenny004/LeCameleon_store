@extends('layouts.admin')

@section('title', 'Autenticación de dos factores')
@section('page-title', 'Autenticación de dos factores')

@section('content')
<div class="two-factor">
    @if (auth()->user()->two_factor_confirmed_at)
        <section class="card two-factor__status" aria-labelledby="two-factor-status-title">
            <h1 id="two-factor-status-title" class="heading-2">Protege tu cuenta</h1>
            <p class="two-factor__status-message">Estado: <span class="badge badge--success">Activada</span></p>
        </section>

        @if (session('recovery_codes'))
            <section class="card two-factor__codes" role="status" aria-labelledby="recovery-codes-title">
                <h2 id="recovery-codes-title" class="card__title">Códigos de recuperación</h2>
                <p>Guárdalos ahora; no volverán a mostrarse después de salir de esta página.</p>
                <ul class="two-factor__code-list">
                    @foreach (session('recovery_codes') as $recoveryCode)
                        <li class="two-factor__code">{{ $recoveryCode }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="card two-factor__management" aria-labelledby="two-factor-management-title">
            <h2 id="two-factor-management-title" class="card__title">Administrar seguridad</h2>
            <form method="POST" action="{{ route('admin.two-factor.regenerate') }}" class="admin-form">
                @csrf
                <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
                <div class="form-group">
                    <label class="form-label" for="regenerate_password">Contraseña actual <span class="form-label__required" aria-hidden="true">*</span></label>
                    <input id="regenerate_password" name="current_password" type="password" placeholder="Tu contraseña actual" autocomplete="current-password" class="form-input @error('current_password') form-input--error @enderror" required @error('current_password') aria-invalid="true" aria-describedby="regenerate-password-error" @enderror>
                    @error('current_password')
                        <span class="form-error" id="regenerate-password-error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn--ghost">Regenerar códigos</button>
            </form>
            <form method="POST" action="{{ route('admin.two-factor.disable') }}" class="admin-form">
                @csrf
                <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
                <div class="form-group">
                    <label class="form-label" for="disable_password">Contraseña actual <span class="form-label__required" aria-hidden="true">*</span></label>
                    <input id="disable_password" name="current_password" type="password" placeholder="Tu contraseña actual" autocomplete="current-password" class="form-input @error('current_password') form-input--error @enderror" required @error('current_password') aria-invalid="true" aria-describedby="disable-password-error" @enderror>
                    @error('current_password')
                        <span class="form-error" id="disable-password-error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn--ghost">Desactivar 2FA</button>
            </form>
        </section>
    @else
        <section class="card" aria-labelledby="two-factor-setup-title">
            <h1 id="two-factor-setup-title" class="heading-2">Protege tu cuenta</h1>
            <p>Escanea este código QR con tu aplicación de autenticación o usa la clave manual.</p>
            <div class="two-factor__qr">{!! $qrCode !!}</div>
            <p class="form-hint">Clave manual: <code>{{ auth()->user()->two_factor_secret }}</code></p>
            <form method="POST" action="{{ route('admin.two-factor.confirm') }}" class="admin-form">
                @csrf
                <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
                <div class="form-group">
                    <label class="form-label" for="code">Código de 6 dígitos <span class="form-label__required" aria-hidden="true">*</span></label>
                    <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" minlength="6" pattern="[0-9]{6}" placeholder="123456" class="form-input @error('code') form-input--error @enderror" required @error('code') aria-invalid="true" aria-describedby="code-error" @enderror>
                    @error('code')
                        <span class="form-error" id="code-error">{{ $message }}</span>
                    @enderror
                </div>
                <button class="btn btn--primary" type="submit">Confirmar</button>
            </form>
        </section>
    @endif
</div>
@endsection
