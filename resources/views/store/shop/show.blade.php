@extends('layouts.store')

@section('title', ($product->name ?? 'Producto') . ' — Le Cameleon')

@section('content')
@php
    $images = collect($product->images ?? [])
        ->map(fn ($img) => is_string($img) ? $img : ($img->path ?? null))
        ->filter()
        ->map(fn ($path) => str_starts_with($path, 'http') || str_starts_with($path, '/')
            ? $path
            : asset('storage/'.$path))
        ->values()
        ->all();

    $stock = max(0, (int) $product->quantity_available - (int) $product->quantity_reserved);
    $condition = $product->condition_grade?->value ?? $product->condition_grade;
    $brandName = $product->brand?->name;
    $era = $product->era_decade;
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
        <span>{{ $product->name }}</span>
    </nav>

    <div x-data="productGallery({{ json_encode($images) }})">
        <div class="product-gallery">
            <div class="product-gallery__main">
                <template x-if="images.length">
                    <img :src="images[active]" alt="{{ $product->name }}">
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
        @if ($era)
            <span class="product-info__era">{{ $era }}</span>
        @endif
        <h1 class="product-info__title">{{ $product->name }}</h1>
        <p class="product-info__price">${{ number_format((float) $product->price, 2) }}</p>

        <div class="product-info__meta">
            @if ($condition)
                <span class="badge badge--accent">{{ $condition }}</span>
            @endif
            @if ($product->is_unique_piece || $stock <= 1)
                <span class="badge badge--warning">Pieza única</span>
            @endif
        </div>

        <p class="product-info__description">{{ $product->description ?? $product->short_description }}</p>

        <dl class="product-info__specs">
            @if ($brandName)
                <div class="product-info__spec"><dt>Marca</dt><dd>{{ $brandName }}</dd></div>
            @endif
            @if ($product->size_label)
                <div class="product-info__spec"><dt>Talla</dt><dd>{{ $product->size_label }}</dd></div>
            @endif
            @if ($era)
                <div class="product-info__spec"><dt>Época</dt><dd>{{ $era }}</dd></div>
            @endif
            @if ($condition)
                <div class="product-info__spec"><dt>Condición</dt><dd>{{ $condition }}</dd></div>
            @endif
        </dl>

        <div class="product-info__actions" x-data="quantityInput(1, {{ max($stock, 1) }})">
            <div class="product-info__qty">
                <button type="button" @click="decrement()" aria-label="Disminuir">−</button>
                <input type="number" name="quantity" x-model="qty" min="1" :max="max" readonly>
                <button type="button" @click="increment()" aria-label="Aumentar">+</button>
            </div>

            @if (Route::has('cart.store') && $stock > 0)
                <form action="{{ route('cart.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" :value="qty">
                    <button type="submit" class="btn btn--primary">Agregar al carrito</button>
                </form>
            @else
                <button type="button" class="btn btn--primary" disabled>Agotado</button>
            @endif

            @if (Route::has('wishlist.store'))
                <form action="{{ route('wishlist.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="btn btn--ghost">Favoritos</button>
                </form>
            @endif
        </div>
    </div>
</div>

@if (($related ?? collect())->isNotEmpty())
    <section class="container section product-recs">
        <div class="section__header">
            <h2 class="section__title">También te puede gustar</h2>
        </div>
        <div class="grid-products">
            @foreach ($related as $item)
                @include('components.product-card', ['product' => $item])
            @endforeach
        </div>
    </section>
@endif

@if (($alsoViewed ?? collect())->isNotEmpty())
    <section class="container section product-recs">
        <div class="section__header">
            <h2 class="section__title">Clientes también vieron</h2>
        </div>
        <div class="grid-products">
            @foreach ($alsoViewed as $item)
                @include('components.product-card', ['product' => $item])
            @endforeach
        </div>
    </section>
@endif
@endsection
