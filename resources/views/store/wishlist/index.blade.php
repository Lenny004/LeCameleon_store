@extends('layouts.store')

@section('title', 'Favoritos — Le Cameleon')

@section('content')
<div class="container wishlist-page">
    @php
        $products = collect($wishlist?->items ?? [])
            ->map(fn ($item) => $item->product)
            ->filter()
            ->values();
    @endphp

    <header class="wishlist-page__header">
        <p class="wishlist-page__eyebrow">Tu selección</p>
        <h1 class="heading-2">Mis favoritos</h1>
        @if ($products->count())
            <p class="wishlist-page__count">
                {{ $products->count() }} {{ $products->count() === 1 ? 'pieza guardada' : 'piezas guardadas' }}
            </p>
        @endif
    </header>

    @if ($products->count())
        <div class="grid-products">
            @foreach ($products as $product)
                @include('components.product-card', ['product' => $product, 'showWishlist' => false])
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
