@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of store performance')

@section('content')
<div class="kpi-grid">
    <div class="kpi-card">
        <p class="kpi-card__label">Revenue (30d)</p>
        <p class="kpi-card__value">${{ number_format($revenue ?? 12450, 0) }}</p>
        <p class="kpi-card__delta">+12% vs last month</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Orders</p>
        <p class="kpi-card__value">{{ $ordersCount ?? 86 }}</p>
        <p class="kpi-card__delta">+8% vs last month</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Low stock</p>
        <p class="kpi-card__value">{{ $lowStockCount ?? 5 }}</p>
        <p class="kpi-card__delta" style="color:var(--warning);">Needs attention</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Conversion</p>
        <p class="kpi-card__value">{{ $conversion ?? '2.4' }}%</p>
        <p class="kpi-card__delta">+0.3% vs last month</p>
    </div>
</div>

<div class="admin-charts">
    <div class="admin-chart-card">
        <h2 class="admin-chart-card__title">Revenue trend</h2>
        <canvas id="chart-sales"
            data-labels='@json($salesLabels ?? ["Jan","Feb","Mar","Apr","May","Jun","Jul"])'
            data-values='@json($salesValues ?? [3200,4100,3800,5200,4800,6100,5400])'>
        </canvas>
    </div>
    <div class="admin-chart-card">
        <h2 class="admin-chart-card__title">Orders by week</h2>
        <canvas id="chart-orders"
            data-labels='@json($ordersLabels ?? ["W1","W2","W3","W4"])'
            data-values='@json($ordersValues ?? [18,22,25,21])'>
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
                @foreach ($recentOrders ?? [
                    ['id' => 'LC-1042', 'customer' => 'María G.', 'status' => 'Processing', 'total' => 189.00, 'date' => '2026-07-14'],
                    ['id' => 'LC-1041', 'customer' => 'Carlos R.', 'status' => 'Shipped', 'total' => 65.00, 'date' => '2026-07-13'],
                    ['id' => 'LC-1040', 'customer' => 'Ana L.', 'status' => 'Delivered', 'total' => 245.00, 'date' => '2026-07-12'],
                ] as $order)
                    <tr>
                        <td>{{ $order['id'] }}</td>
                        <td>{{ $order['customer'] }}</td>
                        <td><span class="badge badge--primary">{{ $order['status'] }}</span></td>
                        <td>${{ number_format($order['total'], 2) }}</td>
                        <td>{{ $order['date'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
