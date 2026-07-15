@extends('layouts.store')

@section('title', 'Mis pedidos — Le Cameleon')

@section('content')
<div class="container account-layout">
    <nav class="account-nav" aria-label="Cuenta">
        @if (Route::has('account.index'))
            <a href="{{ route('account.index') }}" class="account-nav__link">Perfil</a>
        @endif
        @if (Route::has('account.orders.index'))
            <a href="{{ route('account.orders.index') }}" class="account-nav__link account-nav__link--active">Mis pedidos</a>
        @endif
        @if (Route::has('account.offers.index'))
            <a href="{{ route('account.offers.index') }}" class="account-nav__link">Mis ofertas</a>
        @endif
        @if (Route::has('wishlist.index'))
            <a href="{{ route('wishlist.index') }}" class="account-nav__link">Favoritos</a>
        @endif
    </nav>

    <div class="account-content">
        <h1 class="heading-2">Mis pedidos</h1>

        @php
            $orders = $orders ?? collect([
                (object) ['id' => 'LC-1001', 'date' => '2026-07-01', 'status' => 'Entregado', 'total' => 209.00, 'items_count' => 2],
                (object) ['id' => 'LC-1002', 'date' => '2026-07-10', 'status' => 'En tránsito', 'total' => 89.00, 'items_count' => 1],
            ]);
        @endphp

        @forelse ($orders as $order)
            <article class="order-card">
                <div class="order-card__header">
                    <div>
                        <p class="order-card__id">Pedido #{{ $order->id }}</p>
                        <p class="order-card__date">{{ $order->date }}</p>
                    </div>
                    <span class="badge badge--{{ ($order->status ?? '') === 'Entregado' ? 'success' : 'primary' }}">{{ $order->status }}</span>
                </div>
                <p class="order-card__items">{{ $order->items_count }} artículo(s)</p>
                <div class="order-card__footer">
                    <span class="order-card__total">${{ number_format($order->total, 2) }}</span>
                    @if (Route::has('account.orders.show'))
                        <a href="{{ route('account.orders.show', $order->id) }}" class="btn btn--ghost btn--sm">Ver detalle</a>
                    @endif
                </div>
            </article>
        @empty
            @include('components.empty-state', [
                'title' => 'Sin pedidos aún',
                'text' => 'Cuando compres algo, aparecerá aquí.',
                'actionLabel' => 'Ir a la tienda',
                'actionUrl' => Route::has('shop.index') ? route('shop.index') : '#',
            ])
        @endforelse
    </div>
</div>
@endsection
