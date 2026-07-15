@extends('layouts.store')

@section('title', 'Carrito — Le Cameleon')

@section('content')
<div class="container cart-page">
    <h1 class="heading-1" style="margin-bottom: var(--space-xl);">Tu carrito</h1>

    @php
        $items = $cartItems ?? collect([
            (object) ['id' => 1, 'name' => 'Chaqueta Denim 80s', 'price' => 89.00, 'quantity' => 1, 'size' => 'M', 'image' => null],
            (object) ['id' => 2, 'name' => 'Vestido Floral 70s', 'price' => 120.00, 'quantity' => 1, 'size' => 'S', 'image' => null],
        ]);
        $subtotal = $items->sum(fn ($i) => $i->price * $i->quantity);
        $shipping = $shipping ?? 9.99;
        $total = $subtotal + $shipping;
    @endphp

    @if ($items->count())
        <div class="cart-layout">
            <div class="cart-items">
                @foreach ($items as $item)
                    <article class="cart-item">
                        <div class="cart-item__image" style="background:var(--secondary);"></div>
                        <div class="cart-item__details">
                            <h2 class="cart-item__title">{{ $item->name }}</h2>
                            @if ($item->size ?? null)
                                <p class="cart-item__meta">Talla: {{ $item->size }}</p>
                            @endif
                            <p class="cart-item__price">${{ number_format($item->price * $item->quantity, 2) }}</p>
                        </div>
                        <div class="cart-item__actions">
                            @if (Route::has('cart.update'))
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display:flex;align-items:center;gap:var(--space-sm);">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-input" style="width:4rem;padding:0.5rem;">
                                    <button type="submit" class="btn btn--ghost btn--sm">Actualizar</button>
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

            <aside class="cart-summary">
                <div class="cart-summary__row">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="cart-summary__row">
                    <span>Envío</span>
                    <span>${{ number_format($shipping, 2) }}</span>
                </div>
                <div class="cart-summary__row cart-summary__row--total">
                    <span>Total</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
                <div class="cart-summary__cta">
                    @if (Route::has('checkout.index'))
                        <a href="{{ route('checkout.index') }}" class="btn btn--primary btn--block">Ir al checkout</a>
                    @endif
                </div>
                <p class="cart-summary__note">Envío calculado en el siguiente paso</p>
            </aside>
        </div>
    @else
        @include('components.empty-state', [
            'title' => 'Tu carrito está vacío',
            'text' => 'Explora la tienda y encuentra piezas vintage únicas.',
            'actionLabel' => 'Ir a la tienda',
            'actionUrl' => Route::has('shop.index') ? route('shop.index') : '#',
        ])
    @endif
</div>
@endsection
