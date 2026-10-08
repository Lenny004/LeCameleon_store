@extends('layouts.guest')

@section('title', 'Crear cuenta — Le Cameleon')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <header class="auth-card__head">
            <span class="auth-card__badge" aria-hidden="true">&#10024;</span>
            <h1 class="auth-card__title">Crear cuenta</h1>
            <p class="auth-card__subtitle">Únete y guarda tus favoritos</p>
        </header>

        <form method="POST" action="{{ Route::has('register') ? route('register') : '#' }}" class="auth-card__form">
            @csrf
            <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
            <div class="form-group">
                <label class="form-label" for="name">Nombre <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="text" id="name" name="name" class="form-input @error('name') form-input--error @enderror" value="{{ old('name') }}" placeholder="María López" maxlength="255" required autofocus autocomplete="name" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                @error('name')<span class="form-error" id="name-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="email" id="email" name="email" class="form-input @error('email') form-input--error @enderror" value="{{ old('email') }}" placeholder="tu@correo.com" maxlength="255" inputmode="email" required autocomplete="email" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')<span class="form-error" id="email-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Contraseña <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="password" id="password" name="password" class="form-input @error('password') form-input--error @enderror" placeholder="Mínimo 8 caracteres" minlength="8" maxlength="255" required autocomplete="new-password" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                @error('password')<span class="form-error" id="password-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirmar contraseña <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input @error('password_confirmation') form-input--error @enderror" placeholder="Repite tu contraseña" maxlength="255" required autocomplete="new-password" @error('password_confirmation') aria-invalid="true" aria-describedby="password_confirmation-error" @enderror>
                @error('password_confirmation')<span class="form-error" id="password_confirmation-error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn btn--primary btn--block">Registrarse</button>
        </form>

        <p class="auth-card__footer">
            @if (Route::has('login'))
                ¿Ya tienes cuenta? <a class="auth-card__footer-link" href="{{ route('login') }}">Inicia sesión</a>
            @endif
        </p>
    </div>
</div>
@endsection
