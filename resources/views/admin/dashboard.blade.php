@extends('layouts.admin')

@section('title', 'Panel principal')
@section('page-title', 'Panel principal')
@section('page-subtitle', 'Resumen del rendimiento de la tienda (últimos 30 días)')

@section('content')
<div class="admin-dashboard">
    <div class="kpi-grid">
        <div class="kpi-card">
            <p class="kpi-card__label">Ingresos (30 días)</p>
            <p class="kpi-card__value">${{ number_format($revenue ?? $kpis['revenue'] ?? 0, 0) }}</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-card__label">Pedidos</p>
            <p class="kpi-card__value">{{ $ordersCount ?? $kpis['orders_count'] ?? 0 }}</p>
        </div>
        <div class="kpi-card kpi-card--alert">
            <p class="kpi-card__label">Poco inventario</p>
            <p class="kpi-card__value">{{ $lowStockCount ?? $kpis['low_stock_count'] ?? 0 }}</p>
            <p class="kpi-card__delta kpi-card__delta--warning">Requiere atención</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-card__label">Conversión</p>
            <p class="kpi-card__value">{{ number_format((float) ($conversion ?? $kpis['conversion_rate'] ?? 0), 1) }}%</p>
        </div>
    </div>

    <div class="kpi-grid kpi-grid--spaced">
        <div class="kpi-card">
            <p class="kpi-card__label">Pedidos pendientes</p>
            <p class="kpi-card__value">{{ $kpis['pending_orders_count'] ?? 0 }}</p>
            @if (Route::has('admin.orders.index'))
                <a href="{{ route('admin.orders.index') }}" class="kpi-card__delta">Ver pedidos</a>
            @endif
        </div>
        <div class="kpi-card">
            <p class="kpi-card__label">Ofertas pendientes</p>
            <p class="kpi-card__value">{{ $kpis['pending_offers_count'] ?? 0 }}</p>
            @if (Route::has('admin.offers.index'))
                <a href="{{ route('admin.offers.index') }}" class="kpi-card__delta">Ver ofertas</a>
            @endif
        </div>
        <div class="kpi-card">
            <p class="kpi-card__label">Devoluciones pendientes</p>
            <p class="kpi-card__value">{{ $kpis['pending_returns_count'] ?? 0 }}</p>
            @if (Route::has('admin.return-requests.index'))
                <a href="{{ route('admin.return-requests.index') }}" class="kpi-card__delta">Ver devoluciones</a>
            @endif
        </div>
        <div class="kpi-card">
            <p class="kpi-card__label">Piezas autenticadas</p>
            <p class="kpi-card__value">{{ $kpis['authenticated_products_count'] ?? 0 }}</p>
            @if (Route::has('admin.products.index'))
                <a href="{{ route('admin.products.index') }}" class="kpi-card__delta">Ver catálogo</a>
            @endif
        </div>
    </div>

    <div class="admin-charts">
        <div class="admin-chart-card">
            <h2 class="admin-chart-card__title">Tendencia de ingresos</h2>
            <canvas class="admin-chart-card__canvas" id="chart-sales"
                data-labels='@json($salesLabels ?? [])'
                data-values='@json($salesValues ?? [])'>
            </canvas>
        </div>
        <div class="admin-chart-card">
            <h2 class="admin-chart-card__title">Pedidos por día</h2>
            <canvas class="admin-chart-card__canvas" id="chart-orders"
                data-labels='@json($ordersLabels ?? [])'
                data-values='@json($ordersValues ?? [])'>
            </canvas>
        </div>
    </div>

    <div class="card admin-dashboard__recent">
        <div class="card__header">
                <h2 class="card__title">Pedidos recientes</h2>
            @if (Route::has('admin.orders.index'))
                <a href="{{ route('admin.orders.index') }}" class="btn btn--ghost btn--sm">Ver todos</a>
            @endif
        </div>
        <div class="table-wrap">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders ?? [] as $order)
                        <tr>
                            <td>
                                @if (Route::has('admin.orders.show'))
                                    <a class="admin-table__link" href="{{ route('admin.orders.show', $order) }}">{{ $order->number }}</a>
                                @else
                                    {{ $order->number }}
                                @endif
                            </td>
                            <td>{{ $order->user?->name ?? '—' }}</td>
                            <td><span class="badge badge--primary">{{ ['pending' => 'Pendiente', 'paid' => 'Pagado', 'processing' => 'En preparación', 'shipped' => 'Enviado', 'delivered' => 'Entregado', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado'][$order->status?->value ?? $order->status] ?? $order->status }}</span></td>
                            <td>${{ number_format((float) $order->grand_total, 2) }}</td>
                            <td>{{ optional($order->placed_at)->toDateString() ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                        <td colspan="5">Aún no hay pedidos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
