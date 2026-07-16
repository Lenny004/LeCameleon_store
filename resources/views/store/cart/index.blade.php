@extends('layouts.store')

@section('title', 'Carrito — Le Cameleon')

@section('content')
<div class="container cart-page">
    @php
        $items = $cartItems ?? collect([
            (object) ['id' => 1, 'name' => 'Chaqueta Denim 80s', 'price' => 89.00, 'quantity' => 1, 'size' => 'M', 'image' => null],
            (object) ['id' => 2, 'name' => 'Vestido Floral 70s', 'price' => 120.00, 'quantity' => 1, 'size' => 'S', 'image' => null],
        ]);
        $subtotal = $items->sum(fn ($i) => $i->price * $i->quantity);
        $shipping = $shipping ?? 9.99;
        $total = $subtotal + $shipping;
    @endphp

    <header class="cart-page__header">
        <h1 class="heading-2">Tu carrito</h1>
        @if ($items->count())
            <p class="cart-page__count">{{ $items->count() }} {{ $items->count() === 1 ? 'artículo' : 'artículos' }}</p>
        @endif
    </header>

    @if ($items->count())
        <div class="cart-layout">
            <div class="cart-items">
                <header class="cart-items__header">
                    <h2 class="cart-items__title">Artículos en tu carrito</h2>
                    <span class="cart-items__meta">{{ $items->count() }} {{ $items->count() === 1 ? 'pieza' : 'piezas' }}</span>
                </header>
                @foreach ($items as $item)
                    <article class="cart-item">
                        @if ($item->image ?? null)
                            <img src="{{ $item->image }}" alt="" class="cart-item__image">
                        @else
                            <div class="cart-item__image cart-item__image--placeholder" aria-hidden="true"></div>
                        @endif
                        <div class="cart-item__details">
                            <h2 class="cart-item__title">{{ $item->name }}</h2>
                            @if ($item->size ?? null)
                                <p class="cart-item__meta">Talla: {{ $item->size }}</p>
                            @endif
                            <p class="cart-item__price">
                                ${{ number_format($item->price * $item->quantity, 2) }}
                                @if ($item->quantity > 1)
                                    <span class="cart-item__unit">(${{ number_format($item->price, 2) }} c/u)</span>
                                @endif
                            </p>
                        </div>
                        <div class="cart-item__actions">
                            @if (Route::has('cart.update'))
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="cart-item__qty-form">
                                    @csrf
                                    @method('PATCH')
                                    <input
                                        type="number"
                                        id="qty-{{ $item->id }}"
                                        name="quantity"
                                        value="{{ $item->quantity }}"
                                        min="1"
                                        class="form-input cart-item__qty-input"
                                        aria-label="Cantidad de {{ $item->name }}"
                                    >
                                    <button type="submit" class="btn btn--secondary btn--sm">Actualizar</button>
                                </form>
                            @endif
                            @if (Route::has('cart.destroy'))
                                <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn--ghost btn--sm">Eliminar</button>
                                </form>
                            @endif
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
                    @if (Route::has('checkout.index'))
                        <a href="{{ route('checkout.index') }}" class="btn btn--primary btn--block btn--lg">Proceder al checkout</a>
                    @endif
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
