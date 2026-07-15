@props([
    'product',
    'showWishlist' => true,
])

@php
    $slug = $product->slug ?? $product['slug'] ?? '#';
    $name = $product->name ?? $product['name'] ?? 'Producto';
    $price = $product->price ?? $product['price'] ?? 0;
    $era = $product->era ?? $product['era'] ?? null;
    $condition = $product->condition ?? $product['condition'] ?? null;
    $image = $product->image ?? $product['image'] ?? $product->thumbnail ?? $product['thumbnail'] ?? null;
    $url = Route::has('shop.show') ? route('shop.show', $slug) : '#';
@endphp

<article class="product-card">
    <a href="{{ $url }}" class="product-card__link">
        <div class="product-card__media">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $name }}" class="product-card__image" loading="lazy">
            @else
                <div class="product-card__image" style="background: var(--secondary);"></div>
            @endif
            @if ($condition)
                <span class="product-card__badge badge badge--accent">{{ $condition }}</span>
            @endif
        </div>
        <div class="product-card__body">
            @if ($era)
                <span class="product-card__era">{{ $era }}</span>
            @endif
            <h3 class="product-card__title">{{ $name }}</h3>
            @if ($condition && !$era)
                <p class="product-card__meta">{{ $condition }}</p>
            @endif
            <div class="product-card__footer">
                <span class="product-card__price">${{ number_format((float) $price, 2) }}</span>
            </div>
        </div>
    </a>
    @if ($showWishlist && Route::has('wishlist.store'))
        <form action="{{ route('wishlist.store') }}" method="POST" style="position:absolute;top:var(--space-sm);right:var(--space-sm);">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id ?? $product['id'] ?? '' }}">
            <button type="submit" class="product-card__wishlist" aria-label="Agregar a favoritos">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </button>
        </form>
    @endif
</article>
