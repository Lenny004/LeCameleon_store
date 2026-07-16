@extends('layouts.store')

@section('title', 'Mi cuenta — Le Cameleon')

@section('content')
<div class="container account-layout">
    <nav class="account-nav" aria-label="Cuenta">
        @if (Route::has('account.index'))
            <a href="{{ route('account.index') }}" class="account-nav__link account-nav__link--active">Perfil</a>
        @endif
        @if (Route::has('account.orders.index'))
            <a href="{{ route('account.orders.index') }}" class="account-nav__link">Mis pedidos</a>
        @endif
        @if (Route::has('wishlist.index'))
            <a href="{{ route('wishlist.index') }}" class="account-nav__link">Favoritos</a>
        @endif
        @if (Route::has('logout'))
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="account-nav__logout">Cerrar sesión</button>
            </form>
        @endif
    </nav>

    <div class="account-content">
        <h1 class="heading-2">Mi perfil</h1>

        <div class="card">
            <form method="POST" action="{{ Route::has('account.update') ? route('account.update') : '#' }}" class="auth-card__form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label" for="name">Nombre</label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name', auth()->user()->name ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" class="form-input" value="{{ old('email', auth()->user()->email ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Teléfono</label>
                    <input type="tel" id="phone" name="phone" class="form-input" value="{{ old('phone', auth()->user()->phone ?? '') }}">
                </div>
                <button type="submit" class="btn btn--primary">Guardar cambios</button>
            </form>
        </div>
    </div>
</div>
@endsection
