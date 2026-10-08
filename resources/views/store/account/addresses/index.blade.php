@extends('layouts.store')

@section('title', 'Mis direcciones — Le Cameleon')

@section('content')
<div class="container account-layout">
    <nav class="account-nav" aria-label="Cuenta">
        @if (Route::has('account.index'))
            <a href="{{ route('account.index') }}" class="account-nav__link">Perfil</a>
        @endif
        @if (Route::has('account.orders.index'))
            <a href="{{ route('account.orders.index') }}" class="account-nav__link">Mis pedidos</a>
        @endif
        @if (Route::has('account.addresses.index'))
            <a href="{{ route('account.addresses.index') }}" class="account-nav__link account-nav__link--active">Direcciones</a>
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
            <p class="account-content__eyebrow">Cuenta</p>
            <h1 class="heading-2 account-content__title">Mis direcciones</h1>
            <a href="{{ route('account.addresses.create') }}" class="btn btn--primary">Agregar dirección</a>
        </header>
        @forelse ($addresses as $address)
            <article class="order-card">
                <div class="order-card__header"><div><h2 class="order-card__id">{{ $address->label ?: 'Dirección de envío' }}</h2><p class="order-card__date">{{ $address->first_name }} {{ $address->last_name }}</p></div>@if ($address->is_default)<span class="badge badge--success">Predeterminada</span>@endif</div>
                <p class="order-card__items">{{ $address->line1 }}{{ $address->line2 ? ', '.$address->line2 : '' }} · {{ $address->city }} · {{ $address->country }}</p>
                @if ($address->municipality)<p class="order-card__items">Municipio: {{ $address->municipality->name }}</p>@endif
                <div class="account-actions">
                    <a class="btn btn--ghost" href="{{ route('account.addresses.edit', $address) }}">Editar</a>
                    @if (! $address->is_default)
                        <form method="POST" action="{{ route('account.addresses.default', $address) }}">@csrf @method('PATCH')<button class="btn btn--ghost" type="submit">Usar como predeterminada</button></form>
                    @endif
                    <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" x-data x-on:submit="if (!confirm('¿Eliminar esta dirección?')) $event.preventDefault()">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn--ghost" type="submit">Eliminar</button>
                    </form>
                </div>
            </article>
        @empty
            <p class="empty-state">Todavía no tienes direcciones guardadas.</p>
        @endforelse
    </div>
</div>
@endsection
