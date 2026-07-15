@extends('layouts.guest')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <p class="auth-card__brand">Le <span>Cameleon</span></p>
        <h1 class="auth-card__title">Iniciar sesión</h1>
        <p class="auth-card__subtitle">Bienvenido de vuelta a tu cuenta</p>

        <form method="POST" action="{{ Route::has('login') ? route('login') : '#' }}" class="auth-card__form">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus autocomplete="email">
                @error('email')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-input" required autocomplete="current-password">
                @error('password')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <label class="form-checkbox">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Recordarme
            </label>
            <button type="submit" class="btn btn--primary btn--block">Entrar</button>
        </form>

        <p class="auth-card__footer">
            @if (Route::has('register'))
                ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a>
            @endif
        </p>
    </div>
</div>
@endsection
