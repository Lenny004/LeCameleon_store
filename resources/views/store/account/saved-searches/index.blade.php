@extends('layouts.store')

@section('title', 'Búsquedas guardadas — Le Cameleon')

@section('content')
<div class="container account-layout">
    <nav class="account-nav" aria-label="Cuenta">
        @if (Route::has('account.index'))
            <a href="{{ route('account.index') }}" class="account-nav__link">Perfil</a>
        @endif
        @if (Route::has('account.orders.index'))
            <a href="{{ route('account.orders.index') }}" class="account-nav__link">Mis pedidos</a>
        @endif
        @if (Route::has('account.offers.index'))
            <a href="{{ route('account.offers.index') }}" class="account-nav__link">Mis ofertas</a>
        @endif
        @if (Route::has('account.saved-searches.index'))
            <a href="{{ route('account.saved-searches.index') }}" class="account-nav__link account-nav__link--active">Búsquedas guardadas</a>
        @endif
        @if (Route::has('wishlist.index'))
            <a href="{{ route('wishlist.index') }}" class="account-nav__link">Favoritos</a>
        @endif
    </nav>

    <div class="account-content">
        <header class="account-content__header">
            <p class="account-content__eyebrow">Curaduría</p>
            <h1 class="heading-2 account-content__title">Búsquedas guardadas</h1>
            <p class="account-content__lead">Vuelve rápido a los filtros que usas para encontrar piezas vintage.</p>
        </header>

        @forelse ($savedSearches as $savedSearch)
            <article class="order-card">
                <div class="order-card__header">
                    <div>
                        <p class="order-card__id">
                            <a href="{{ $savedSearch->shopUrl() }}">{{ $savedSearch->name }}</a>
                        </p>
                        <p class="order-card__date">{{ $savedSearch->created_at?->format('Y-m-d H:i') }}</p>
                    </div>
                    @if (Route::has('account.saved-searches.destroy'))
                        <form method="POST" action="{{ route('account.saved-searches.destroy', $savedSearch) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm">Eliminar</button>
                        </form>
                    @endif
                </div>
                <p class="order-card__items order-card__note">
                    {{ collect($savedSearch->query_params)->map(fn ($value, $key) => $key.'='.$value)->implode(' · ') }}
                </p>
            </article>
        @empty
            @include('components.empty-state', [
                'title' => 'Sin búsquedas guardadas',
                'text' => 'Aplica filtros en la tienda y guarda la búsqueda para volver más tarde.',
                'actionLabel' => __('store.nav_shop'),
                'actionUrl' => Route::has('shop.index') ? route('shop.index') : '#',
            ])
        @endforelse

        @if ($savedSearches->hasPages())
            <div class="account-pagination">
                {{ $savedSearches->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
