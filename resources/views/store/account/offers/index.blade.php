@extends('layouts.store')

@section('title', 'Mis ofertas — Le Cameleon')

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
            <a href="{{ route('account.offers.index') }}" class="account-nav__link account-nav__link--active">Mis ofertas</a>
        @endif
        @if (Route::has('account.saved-searches.index'))
            <a href="{{ route('account.saved-searches.index') }}" class="account-nav__link">Búsquedas guardadas</a>
        @endif
        @if (Route::has('wishlist.index'))
            <a href="{{ route('wishlist.index') }}" class="account-nav__link">Favoritos</a>
        @endif
    </nav>

    <div class="account-content">
        <h1 class="heading-2">Mis ofertas</h1>

        @forelse ($offers as $offer)
            <article class="order-card">
                <div class="order-card__header">
                    <div>
                        <p class="order-card__id">
                            @if ($offer->product && Route::has('shop.show'))
                                <a href="{{ route('shop.show', $offer->product->slug) }}">{{ $offer->product->name }}</a>
                            @else
                                {{ $offer->product?->name ?? 'Producto' }}
                            @endif
                        </p>
                        <p class="order-card__date">{{ $offer->created_at?->format('Y-m-d H:i') }}</p>
                    </div>
                    @php
                        $statusBadge = match ($offer->status->value) {
                            'accepted' => 'success',
                            'declined' => 'error',
                            'countered' => 'primary',
                            default => 'warning',
                        };
                    @endphp
                    <span class="badge badge--{{ $statusBadge }}">{{ ucfirst($offer->status->value) }}</span>
                </div>
                <p class="order-card__items">
                    Tu oferta: ${{ number_format((float) $offer->amount, 2) }}
                    @if ($offer->product)
                        · Precio de lista: ${{ number_format((float) $offer->product->price, 2) }}
                    @endif
                </p>
                @if ($offer->counter_amount)
                    <p class="order-card__items">Contraoferta: ${{ number_format((float) $offer->counter_amount, 2) }}</p>
                @endif
                @if ($offer->message)
                    <p class="text-muted" style="font-size:0.9rem;">{{ $offer->message }}</p>
                @endif
                @if ($offer->admin_notes)
                    <p class="text-muted" style="font-size:0.9rem;">Notas: {{ $offer->admin_notes }}</p>
                @endif
            </article>
        @empty
            @include('components.empty-state', [
                'title' => 'Sin ofertas aún',
                'text' => 'Haz una oferta en piezas de la tienda para negociar el precio.',
                'actionLabel' => 'Ir a la tienda',
                'actionUrl' => Route::has('shop.index') ? route('shop.index') : '#',
            ])
        @endforelse

        @if ($offers->hasPages())
            <div style="margin-top:var(--space-lg);">
                {{ $offers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
