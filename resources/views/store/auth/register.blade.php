@extends('layouts.guest')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <p class="auth-card__brand">Le <span>Cameleon</span></p>
        <h1 class="auth-card__title">Crear cuenta</h1>
        <p class="auth-card__subtitle">Únete y guarda tus favoritos</p>

        <form method="POST" action="{{ Route::has('register') ? route('register') : '#' }}" class="auth-card__form">
            @csrf
            <div class="form-group">
                <label class="form-label" for="name">Nombre</label>
                <input type="text" id="name" name="name" class="form-input @error('name') form-input--error @enderror" value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" class="form-input @error('email') form-input--error @enderror" value="{{ old('email') }}" required autocomplete="email">
                @error('email')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-input @error('password') form-input--error @enderror" required autocomplete="new-password">
                @error('password')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn--primary btn--block">Registrarse</button>
        </form>

        <p class="auth-card__footer">
            @if (Route::has('login'))
                ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
            @endif
        </p>
    </div>
</div>
@endsection
