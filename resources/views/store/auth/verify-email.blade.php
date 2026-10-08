@extends('layouts.guest')

@section('title', 'Verifica tu correo — Le Cameleon')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <header class="auth-card__head">
            <h1 class="auth-card__title">Verifica tu correo</h1>
            <p class="auth-card__subtitle">Confirma tu dirección para acceder a tu cuenta.</p>
        </header>
        <form method="POST" action="{{ route('verification.send') }}" class="auth-card__form">
            @csrf
            <button type="submit" class="btn btn--primary btn--block">Reenviar enlace</button>
        </form>
        <form method="POST" action="{{ route('logout') }}" class="auth-card__form">
            @csrf
            <button type="submit" class="btn btn--ghost btn--block">Cerrar sesión</button>
        </form>
    </div>
</div>
@endsection
