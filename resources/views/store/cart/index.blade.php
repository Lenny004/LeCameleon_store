@extends('layouts.store')

@section('title', 'Carrito — Le Cameleon')

@section('content')
<div class="container cart-page">
    @php
        use App\Models\ProductImage;

        $items = $cart->items ?? collect();
        $subtotal = (float) ($subtotal ?? 0);
        $shipping = (float) ($shipping ?? config('store.shipping_flat_rate', 0));
        $total = $subtotal + $shipping;
    @endphp

    <header class="cart-page__header">
        <h1 class="heading-2">Tu carrito</h1>
        @if ($items->count())
            <p class="cart-page__count">
                {{ $itemCount ?? $items->sum('quantity') }}
                {{ ($itemCount ?? $items->sum('quantity')) === 1 ? 'artículo' : 'artículos' }}
            </p>
        @endif
    </header>

    @if ($items->count())
        <div class="cart-layout">
            <div class="cart-items">
                <header class="cart-items__header">
                    <h2 class="cart-items__title">Artículos en tu carrito</h2>
                    <span class="cart-items__meta">
                        {{ $items->count() }} {{ $items->count() === 1 ? 'pieza' : 'piezas' }}
                    </span>
                </header>
                @foreach ($items as $item)
                    @php
                        $product = $item->product;
                        $imageUrl = ProductImage::urlFor($product?->images->first());
                    @endphp
                    <article class="cart-item">
                        <img src="{{ $imageUrl }}" alt="" class="cart-item__image">
                        <div class="cart-item__details">
                            <h2 class="cart-item__title">{{ $product?->name ?? 'Producto' }}</h2>
                            @if ($product?->size_label)
                                <p class="cart-item__meta">Talla: {{ $product->size_label }}</p>
                            @endif
                            <p class="cart-item__price">
                                ${{ number_format((float) $item->unit_price * $item->quantity, 2) }}
                                @if ($item->quantity > 1)
                                    <span class="cart-item__unit">(${{ number_format((float) $item->unit_price, 2) }} c/u)</span>
                                @endif
                            </p>
                        </div>
                        <div class="cart-item__actions">
                            <form action="{{ route('cart.update', $item) }}" method="POST" class="cart-item__qty-form">
                                @csrf
                                @method('PATCH')
                                <input
                                    type="number"
                                    id="qty-{{ $item->id }}"
                                    name="quantity"
                                    value="{{ $item->quantity }}"
                                    min="0"
                                    max="99"
                                    class="form-input cart-item__qty-input @error('quantity') form-input--error @enderror"
                                    aria-label="Cantidad de {{ $product?->name ?? 'producto' }}"
                                >
                                @error('quantity')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="btn btn--secondary btn--sm">Actualizar</button>
                            </form>
                            <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn--ghost btn--sm">Eliminar</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <aside class="order-summary">
                <h2 class="order-summary__title">Resumen del pedido</h2>
                <div class="cart-summary__rows">
                    <div class="cart-summary__row">
                        <span class="cart-summary__row-label">Subtotal</span>
                        <span class="cart-summary__row-value">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="cart-summary__row">
                        <span class="cart-summary__row-label">Envío estimado</span>
                        <span class="cart-summary__row-value">${{ number_format($shipping, 2) }}</span>
                    </div>
                    <div class="cart-summary__row cart-summary__row--total">
                        <span class="cart-summary__row-label">Total</span>
                        <span class="cart-summary__row-value">${{ number_format($total, 2) }}</span>
                    </div>
                </div>
                <div class="cart-summary__cta">
                    <a href="{{ route('checkout.index') }}" class="btn btn--primary btn--block btn--lg">Proceder al checkout</a>
                </div>
                <p class="cart-summary__note">El envío final se confirma en el siguiente paso según tu municipio.</p>
                <div class="cart-summary__trust">
                    <span class="cart-summary__trust-item">Embalaje cuidadoso para piezas vintage</span>
                    <span class="cart-summary__trust-item">Descripciones honestas del estado</span>
                </div>
                @if (Route::has('shop.index'))
                    <p class="cart-continue">
                        <a href="{{ route('shop.index') }}">← Seguir comprando</a>
                    </p>
                @endif
            </aside>
        </div>
    @else
        @include('components.empty-state', [
            'title' => 'Tu carrito está vacío',
            'text' => 'Explora la tienda y encuentra piezas vintage únicas con historias que merecen seguir contándose.',
            'actionLabel' => 'Ir a la tienda',
            'actionUrl' => Route::has('shop.index') ? route('shop.index') : '#',
        ])
    @endif
</div>
@endsection
