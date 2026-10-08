@extends('layouts.admin')

@section('title', 'Pedidos')
@section('page-title', 'Pedidos')
@section('page-subtitle', 'Flujo y preparación de pedidos')

@section('content')
@php
    $statusLabels = [
        'pending' => 'Pendiente',
        'paid' => 'Pagado',
        'processing' => 'En preparación',
        'shipped' => 'Enviado',
        'delivered' => 'Entregado',
        'cancelled' => 'Cancelado',
        'refunded' => 'Reembolsado',
    ];
@endphp
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Todos los pedidos</h2>
        <p class="admin-page-header__subtitle">{{ $orders->total() }} pedidos</p>
    </div>
</div>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Pedido</th>
                <th>Cliente</th>
                <th>Artículos</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>
                        @if (Route::has('admin.orders.show'))
                            <a class="admin-table__link" href="{{ route('admin.orders.show', $order) }}">{{ $order->number }}</a>
                        @else
                            {{ $order->number }}
                        @endif
                    </td>
                    <td>{{ $order->customerEmail() ?? $order->user?->email ?? '—' }}</td>
                    <td>{{ $order->items_count }}</td>
                    <td>${{ number_format((float) $order->grand_total, 2) }}</td>
                    <td><span class="badge badge--primary">{{ $statusLabels[$order->status->value] ?? $order->status->value }}</span></td>
                    <td>{{ $order->placed_at?->format('Y-m-d') ?? '—' }}</td>
                    <td>
                        @if (Route::has('admin.orders.show'))
                            <a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.orders.show', $order) }}">Ver</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aún no hay pedidos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($orders->hasPages())
    <div class="admin-pagination">
        {{ $orders->links() }}
    </div>
@endif
@endsection
