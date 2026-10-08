@extends('layouts.guest')

@section('title', 'Iniciar sesión — Le Cameleon')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <header class="auth-card__head">
            <span class="auth-card__badge" aria-hidden="true">&#128274;</span>
            <h1 class="auth-card__title">Iniciar sesión</h1>
            <p class="auth-card__subtitle">Bienvenido de vuelta a tu cuenta</p>
        </header>

        <form method="POST" action="{{ Route::has('login') ? route('login') : '#' }}" class="auth-card__form">
            @csrf
            <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="email" id="email" name="email" class="form-input @error('email') form-input--error @enderror" value="{{ old('email') }}" placeholder="tu@correo.com" maxlength="255" inputmode="email" required autofocus autocomplete="email" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')<span class="form-error" id="email-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Contraseña <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="password" id="password" name="password" class="form-input @error('password') form-input--error @enderror" placeholder="Tu contraseña" maxlength="255" required autocomplete="current-password" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                @error('password')<span class="form-error" id="password-error">{{ $message }}</span>@enderror
            </div>
            <label class="form-checkbox">
                <input type="checkbox" class="form-checkbox__input" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Recordarme
            </label>
            <button type="submit" class="btn btn--primary btn--block">Entrar</button>
        </form>

        <p class="auth-card__footer">
            @if (Route::has('password.request'))
                <a class="auth-card__footer-link" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            @endif
        </p>
        <p class="auth-card__footer">
            @if (Route::has('register'))
                ¿No tienes cuenta? <a class="auth-card__footer-link" href="{{ route('register') }}">Regístrate</a>
            @endif
        </p>
    </div>
</div>
@endsection
