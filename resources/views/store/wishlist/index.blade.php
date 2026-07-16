@extends('layouts.store')

@section('title', 'Favoritos — Le Cameleon')

@section('content')
<div class="container wishlist-page">
    @php
        $items = collect($wishlist?->items ?? [])
            ->filter(fn ($item) => $item->product !== null)
            ->values();
    @endphp

    <header class="wishlist-page__header">
        <p class="wishlist-page__eyebrow">Tu selección</p>
        <h1 class="heading-2">Mis favoritos</h1>
        @if ($items->count())
            <p class="wishlist-page__count">
                {{ $items->count() }} {{ $items->count() === 1 ? 'pieza guardada' : 'piezas guardadas' }}
            </p>
        @endif
    </header>

    @if ($items->count())
        <div class="grid-products">
            @foreach ($items as $item)
                <div class="wishlist-page__item">
                    @include('components.product-card', ['product' => $item->product, 'showWishlist' => false])
                    @if (Route::has('wishlist.destroy'))
                        <form action="{{ route('wishlist.destroy', $item) }}" method="POST" class="wishlist-page__remove">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm">Quitar de favoritos</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        @include('components.empty-state', [
            'title' => 'Sin favoritos',
            'text' => 'Guarda piezas que te gusten para encontrarlas después.',
            'actionLabel' => 'Explorar tienda',
            'actionUrl' => Route::has('shop.index') ? route('shop.index') : '#',
        ])
    @endif
</div>
@endsection
