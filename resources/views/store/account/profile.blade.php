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
        @if (Route::has('account.addresses.index'))
            <a href="{{ route('account.addresses.index') }}" class="account-nav__link">Direcciones</a>
        @endif
        @if (Route::has('account.offers.index'))
            <a href="{{ route('account.offers.index') }}" class="account-nav__link">Mis ofertas</a>
        @endif
        @if (Route::has('account.saved-searches.index'))
            <a href="{{ route('account.saved-searches.index') }}" class="account-nav__link">Búsquedas guardadas</a>
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
        <header class="account-content__header">
            <p class="account-content__eyebrow">Mi cuenta</p>
            <h1 class="heading-2 account-content__title">Mi perfil</h1>
            <p class="account-content__lead">Actualiza tus datos de contacto para pedidos y envíos.</p>
        </header>

        <div class="account-panel">
            <form method="POST" action="{{ route('account.profile.update') }}" class="auth-card__form">
                @csrf
                @method('PUT')
                <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
                <div class="form-group">
                    <label class="form-label" for="name">Nombre <span class="form-label__required" aria-hidden="true">*</span></label>
                    <input type="text" id="name" name="name" class="form-input @error('name') form-input--error @enderror" value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="María López" maxlength="255" required autocomplete="name" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                    @error('name')<span class="form-error" id="name-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Correo electrónico <span class="form-label__required" aria-hidden="true">*</span></label>
                    <input type="email" id="email" name="email" class="form-input @error('email') form-input--error @enderror" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="tu@correo.com" maxlength="255" inputmode="email" required autocomplete="email" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email')<span class="form-error" id="email-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Teléfono</label>
                    <input type="tel" id="phone" name="phone" class="form-input @error('phone') form-input--error @enderror" value="{{ old('phone', auth()->user()->phone ?? '') }}" placeholder="7777-7777" maxlength="30" inputmode="tel" autocomplete="tel" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
                    @error('phone')<span class="form-error" id="phone-error">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="btn btn--primary">Guardar cambios</button>
            </form>

            <form method="POST" action="{{ route('account.password.update') }}" class="auth-card__form">
                @csrf
                @method('PUT')
                <h2 class="heading-3">Cambiar contraseña</h2>
                <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
                <div class="form-group">
                    <label class="form-label" for="current_password">Contraseña actual <span class="form-label__required" aria-hidden="true">*</span></label>
                    <input type="password" id="current_password" name="current_password" class="form-input @error('current_password') form-input--error @enderror" placeholder="Tu contraseña actual" maxlength="255" autocomplete="current-password" required @error('current_password') aria-invalid="true" aria-describedby="current_password-error" @enderror>
                    @error('current_password')<span class="form-error" id="current_password-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="account_password">Nueva contraseña <span class="form-label__required" aria-hidden="true">*</span></label>
                    <input type="password" id="account_password" name="password" class="form-input @error('password') form-input--error @enderror" placeholder="Mínimo 8 caracteres" minlength="8" maxlength="255" autocomplete="new-password" required @error('password') aria-invalid="true" aria-describedby="account-password-error" @enderror>
                    @error('password')<span class="form-error" id="account-password-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="account_password_confirmation">Confirmar contraseña <span class="form-label__required" aria-hidden="true">*</span></label>
                    <input type="password" id="account_password_confirmation" name="password_confirmation" class="form-input @error('password_confirmation') form-input--error @enderror" placeholder="Repite tu contraseña" maxlength="255" autocomplete="new-password" required @error('password_confirmation') aria-invalid="true" aria-describedby="account-password-confirmation-error" @enderror>
                    @error('password_confirmation')<span class="form-error" id="account-password-confirmation-error">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="btn btn--ghost">Actualizar contraseña</button>
            </form>
        </div>
    </div>
</div>
@endsection
