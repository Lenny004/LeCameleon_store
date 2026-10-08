@extends('layouts.guest')

@section('title', 'Acceso denegado — Le Cameleon')

@section('content')
<section class="error-page" aria-labelledby="error-title-403">
    <div class="error-page__card">
        <div class="error-page__brand">
            @include('components.brand-logo', ['variant' => 'compact', 'size' => 'md'])
        </div>
        <p class="error-page__code" aria-hidden="true">403</p>
        <h1 id="error-title-403" class="error-page__title">No tienes permiso para entrar aquí</h1>
        <p class="error-page__text">
            Esta zona es privada. Si crees que deberías tener acceso, inicia sesión con una cuenta autorizada.
        </p>
        <div class="error-page__actions">
            @if (Route::has('home'))
                <a href="{{ route('home') }}" class="btn btn--primary">Volver al inicio</a>
            @endif
            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn--ghost">Cerrar sesión</button>
                </form>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn btn--ghost">Iniciar sesión</a>
                @endif
            @endauth
        </div>
    </div>
</section>
@endsection
