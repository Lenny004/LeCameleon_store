@extends('layouts.guest')

@section('title', 'Restablecer contraseña — Le Cameleon')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <header class="auth-card__head">
            <h1 class="auth-card__title">Restablecer contraseña</h1>
            <p class="auth-card__subtitle">Elige una nueva contraseña para tu cuenta.</p>
        </header>
        <form method="POST" action="{{ route('password.update') }}" class="auth-card__form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="email" id="email" name="email" class="form-input @error('email') form-input--error @enderror" value="{{ old('email', $email) }}" placeholder="tu@correo.com" maxlength="255" inputmode="email" autocomplete="email" required @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')<span class="form-error" id="email-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Contraseña <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="password" id="password" name="password" class="form-input @error('password') form-input--error @enderror" placeholder="Mínimo 8 caracteres" minlength="8" maxlength="255" autocomplete="new-password" required @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                @error('password')<span class="form-error" id="password-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirmar contraseña <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input @error('password_confirmation') form-input--error @enderror" placeholder="Repite tu contraseña" maxlength="255" autocomplete="new-password" required @error('password_confirmation') aria-invalid="true" aria-describedby="password_confirmation-error" @enderror>
                @error('password_confirmation')<span class="form-error" id="password_confirmation-error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn btn--primary btn--block">Restablecer contraseña</button>
        </form>
    </div>
</div>
@endsection
