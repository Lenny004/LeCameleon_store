@props([
    'product',
    'showWishlist' => true,
])

@php
    use App\Models\ProductImage;

    $get = static function ($product, string $key, mixed $default = null): mixed {
        if (is_array($product)) {
            return $product[$key] ?? $default;
        }

        if (is_object($product)) {
            return data_get($product, $key, $default);
        }

        return $default;
    };

    $resolveImageUrl = static function ($product) use ($get): ?string {
        if (is_object($product) && method_exists($product, 'images') && $product->relationLoaded('images')) {
            $first = $product->images->first();

            if ($first instanceof ProductImage) {
                return $first->url();
            }
        }

        $firstImage = data_get($product, 'images.0');

        if ($firstImage instanceof ProductImage) {
            return $firstImage->url();
        }

        $path = data_get($product, 'images.0.path') ?? data_get($product, 'primaryImage.path');

        if (is_string($path) && $path !== '') {
            return ProductImage::urlFor($path);
        }

        $direct = $get($product, 'image') ?? $get($product, 'thumbnail');

        if (is_string($direct) && $direct !== '') {
            return $direct;
        }

        return null;
    };

    $slug = $get($product, 'slug', '#');
    $name = $get($product, 'name', 'Producto');
    $price = $get($product, 'price', 0);
    $era = $get($product, 'era_decade') ?? $get($product, 'era');
    $condition = $get($product, 'condition_grade') ?? $get($product, 'condition');
    if (is_object($condition) && property_exists($condition, 'value')) {
        $condition = $condition->value;
    }
    $imageUrl = $resolveImageUrl($product);
    $productId = $get($product, 'id', '');
    $url = Route::has('shop.show') && $slug !== '#' ? route('shop.show', $slug) : '#';
@endphp

<article class="product-card">
    <a href="{{ $url }}" class="product-card__link">
        <div class="product-card__media">
            @if ($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $name }}" class="product-card__image" loading="lazy">
            @else
                <div class="product-card__image" style="background: var(--secondary);" aria-hidden="true"></div>
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
            <div class="product-card__footer">
                <span class="product-card__price">${{ number_format((float) $price, 2) }}</span>
            </div>
        </div>
    </a>
    @if ($showWishlist && Route::has('wishlist.store') && $productId)
        <form action="{{ route('wishlist.store') }}" method="POST" class="product-card__wishlist-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $productId }}">
            <button type="submit" class="product-card__wishlist" aria-label="Agregar a favoritos">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </button>
        </form>
    @endif
</article>
