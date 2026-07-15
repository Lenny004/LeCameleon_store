@extends('layouts.store')

@section('title', 'Pedido #' . ($order->id ?? '') . ' — Le Cameleon')

@section('content')
@php
    $order = $order ?? (object) [
        'id' => 'LC-1001',
        'date' => '2026-07-01',
        'status' => 'Entregado',
        'total' => 209.00,
        'shipping_address' => 'Calle Ejemplo 123, Ciudad',
        'items' => [
            (object) ['name' => 'Chaqueta Denim 80s', 'price' => 89.00, 'quantity' => 1],
            (object) ['name' => 'Vestido Floral 70s', 'price' => 120.00, 'quantity' => 1],
        ],
    ];
@endphp

<div class="container" style="padding-block:var(--space-xl);">
    <nav class="breadcrumb">
        @if (Route::has('account.orders'))
            <a href="{{ route('account.orders') }}">Mis pedidos</a>
            <span class="breadcrumb__sep">/</span>
        @endif
        <span>#{{ $order->id }}</span>
    </nav>

    <div class="account-content" style="max-width:40rem;">
        <div class="order-card">
            <div class="order-card__header">
                <div>
                    <h1 class="heading-2">Pedido #{{ $order->id }}</h1>
                    <p class="order-card__date">{{ $order->date }}</p>
                </div>
                <span class="badge badge--success">{{ $order->status }}</span>
            </div>
            <p class="text-muted" style="margin:var(--space-md) 0;">Envío a: {{ $order->shipping_address }}</p>

            @foreach ($order->items as $item)
                <div class="checkout-review-item">
                    <span>{{ $item->name }} × {{ $item->quantity }}</span>
                    <span>${{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
            @endforeach

            <div class="order-card__footer">
                <span class="order-card__total">Total: ${{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
