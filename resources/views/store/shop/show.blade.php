@extends('layouts.store')

@section('title', ($product->name ?? 'Producto') . ' — Le Cameleon')

@section('content')
@php
    $p = $product ?? (object) [
        'id' => 1,
        'slug' => 'chaqueta-denim-80s',
        'name' => 'Chaqueta Denim 80s',
        'price' => 89.00,
        'era' => '1980s',
        'condition' => 'Excelente',
        'brand' => 'Levi\'s',
        'size' => 'M',
        'description' => 'Chaqueta de mezclilla auténtica de los 80. Lavado natural, sin desgaste estructural. Pieza única con historia.',
        'stock' => 1,
        'images' => [],
    ];
    $images = $p->images ?? ($p->image ? [$p->image] : []);
@endphp

<div class="container product-detail">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        @if (Route::has('home'))
            <a href="{{ route('home') }}">Inicio</a>
            <span class="breadcrumb__sep">/</span>
        @endif
        @if (Route::has('shop.index'))
            <a href="{{ route('shop.index') }}">Tienda</a>
            <span class="breadcrumb__sep">/</span>
        @endif
        <span>{{ $p->name }}</span>
    </nav>

    <div x-data="productGallery({{ json_encode($images) }})">
        <div class="product-gallery">
            <div class="product-gallery__main">
                <template x-if="images.length">
                    <img :src="images[active]" alt="{{ $p->name }}">
                </template>
                <template x-if="!images.length">
                    <div style="width:100%;height:100%;background:var(--secondary);"></div>
                </template>
            </div>
            <div class="product-gallery__thumbs" x-show="images.length > 1">
                <template x-for="(img, i) in images" :key="i">
                    <button type="button" class="product-gallery__thumb" :class="{ 'product-gallery__thumb--active': active === i }" @click="select(i)">
                        <img :src="img" alt="">
                    </button>
                </template>
            </div>
        </div>
    </div>

    <div class="product-info">
        @if ($p->era ?? null)
            <span class="product-info__era">{{ $p->era }}</span>
        @endif
        <h1 class="product-info__title">{{ $p->name }}</h1>
        <p class="product-info__price">${{ number_format((float) $p->price, 2) }}</p>

        <div class="product-info__meta">
            @if ($p->condition ?? null)
                <span class="badge badge--accent">{{ $p->condition }}</span>
            @endif
            @if (($p->stock ?? 1) <= 1)
                <span class="badge badge--warning">Pieza única</span>
            @endif
        </div>

        <p class="product-info__description">{{ $p->description ?? '' }}</p>

        <dl class="product-info__specs">
            @if ($p->brand ?? null)
                <div class="product-info__spec"><dt>Marca</dt><dd>{{ $p->brand }}</dd></div>
            @endif
            @if ($p->size ?? null)
                <div class="product-info__spec"><dt>Talla</dt><dd>{{ $p->size }}</dd></div>
            @endif
            @if ($p->era ?? null)
                <div class="product-info__spec"><dt>Época</dt><dd>{{ $p->era }}</dd></div>
            @endif
            @if ($p->condition ?? null)
                <div class="product-info__spec"><dt>Condición</dt><dd>{{ $p->condition }}</dd></div>
            @endif
        </dl>

        <div class="product-info__actions" x-data="quantityInput(1, {{ $p->stock ?? 1 }})">
            <div class="product-info__qty">
                <button type="button" @click="decrement()" aria-label="Disminuir">−</button>
                <input type="number" name="quantity" x-model="qty" min="1" :max="max" readonly>
                <button type="button" @click="increment()" aria-label="Aumentar">+</button>
            </div>

            @if (Route::has('cart.store'))
                <form action="{{ route('cart.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                    <input type="hidden" name="quantity" :value="qty">
                    <button type="submit" class="btn btn--primary">Agregar al carrito</button>
                </form>
            @else
                <button type="button" class="btn btn--primary" disabled>Agregar al carrito</button>
            @endif

            @if (Route::has('wishlist.store'))
                <form action="{{ route('wishlist.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                    <button type="submit" class="btn btn--ghost">Favoritos</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
