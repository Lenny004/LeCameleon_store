@extends('layouts.guest')

@section('title', 'Verificación de seguridad')

@section('content')
<div class="auth-card">
    <h1 class="heading-2">Verificación de seguridad</h1>
    <p>Escribe tu código de autenticación o uno de recuperación.</p>
    <form method="POST" action="{{ route('two-factor.challenge.verify') }}">
        @csrf
        <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
        <div class="form-group">
            <label class="form-label" for="code">Código <span class="form-label__required" aria-hidden="true">*</span></label>
            <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="20" placeholder="123456" class="form-input @error('code') form-input--error @enderror" required @error('code') aria-invalid="true" aria-describedby="code-error" @enderror>
            @error('code')
                <span class="form-error" id="code-error">{{ $message }}</span>
            @enderror
        </div>
        <button class="btn btn--primary" type="submit">Continuar</button>
    </form>
    <form method="POST" action="{{ route('logout') }}" class="auth-card__form">
        @csrf
        <button type="submit" class="btn btn--ghost">Cerrar sesión</button>
    </form>
</div>
@endsection
