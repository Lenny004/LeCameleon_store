@extends('layouts.guest')

@section('title', 'Recuperar contraseña — Le Cameleon')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <header class="auth-card__head">
            <h1 class="auth-card__title">Recuperar contraseña</h1>
            <p class="auth-card__subtitle">Te enviaremos instrucciones si encontramos una cuenta.</p>
        </header>
        <form method="POST" action="{{ route('password.email') }}" class="auth-card__form">
            @csrf
            <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="email" id="email" name="email" class="form-input @error('email') form-input--error @enderror" value="{{ old('email') }}" placeholder="tu@correo.com" maxlength="255" inputmode="email" autocomplete="email" required @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')<span class="form-error" id="email-error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn btn--primary btn--block">Enviar enlace</button>
        </form>
        <p class="auth-card__footer"><a class="auth-card__footer-link" href="{{ route('login') }}">Volver a iniciar sesión</a></p>
    </div>
</div>
@endsection
