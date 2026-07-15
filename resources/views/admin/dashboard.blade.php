@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of store performance (last 30 days)')

@section('content')
<div class="kpi-grid">
    <div class="kpi-card">
        <p class="kpi-card__label">Revenue (30d)</p>
        <p class="kpi-card__value">${{ number_format($revenue ?? $kpis['revenue'] ?? 0, 0) }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Orders</p>
        <p class="kpi-card__value">{{ $ordersCount ?? $kpis['orders_count'] ?? 0 }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Low stock</p>
        <p class="kpi-card__value">{{ $lowStockCount ?? $kpis['low_stock_count'] ?? 0 }}</p>
        <p class="kpi-card__delta" style="color:var(--warning);">Needs attention</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Conversion</p>
        <p class="kpi-card__value">{{ number_format((float) ($conversion ?? $kpis['conversion_rate'] ?? 0), 1) }}%</p>
    </div>
</div>

<div class="admin-charts">
    <div class="admin-chart-card">
        <h2 class="admin-chart-card__title">Revenue trend</h2>
        <canvas id="chart-sales"
            data-labels='@json($salesLabels ?? [])'
            data-values='@json($salesValues ?? [])'>
        </canvas>
    </div>
    <div class="admin-chart-card">
        <h2 class="admin-chart-card__title">Orders by day</h2>
        <canvas id="chart-orders"
            data-labels='@json($ordersLabels ?? [])'
            data-values='@json($ordersValues ?? [])'>
        </canvas>
    </div>
</div>

<div class="card" style="margin-top:var(--space-xl);">
    <div class="card__header">
        <h2 class="card__title">Recent orders</h2>
        @if (Route::has('admin.orders.index'))
            <a href="{{ route('admin.orders.index') }}" class="btn btn--ghost btn--sm">View all</a>
        @endif
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentOrders ?? [] as $order)
                    <tr>
                        <td>
                            @if (Route::has('admin.orders.show'))
                                <a href="{{ route('admin.orders.show', $order) }}">{{ $order->number }}</a>
                            @else
                                {{ $order->number }}
                            @endif
                        </td>
                        <td>{{ $order->user?->name ?? '—' }}</td>
                        <td><span class="badge badge--primary">{{ $order->status?->value ?? $order->status }}</span></td>
                        <td>${{ number_format((float) $order->grand_total, 2) }}</td>
                        <td>{{ optional($order->placed_at)->toDateString() ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No orders yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
