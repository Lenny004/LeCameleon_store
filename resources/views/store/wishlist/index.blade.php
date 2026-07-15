@extends('layouts.store')

@section('title', 'Favoritos — Le Cameleon')

@section('content')
<div class="container section">
    <div class="section__header">
        <h1 class="section__title">Mis favoritos</h1>
    </div>

    @php
        $products = $wishlistProducts ?? collect([
            (object) ['id' => 1, 'slug' => 'chaqueta-denim-80s', 'name' => 'Chaqueta Denim 80s', 'price' => 89.00, 'era' => '1980s', 'condition' => 'Excelente'],
            (object) ['id' => 3, 'slug' => 'bolso-cuero-vintage', 'name' => 'Bolso Cuero Vintage', 'price' => 65.00, 'era' => '1990s', 'condition' => 'Bueno'],
        ]);
    @endphp

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
