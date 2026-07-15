@extends('layouts.admin')

@section('title', 'Analytics')
@section('page-title', 'Analytics')
@section('page-subtitle', 'Sales and traffic reports')

@section('content')
<div class="kpi-grid">
    <div class="kpi-card">
        <p class="kpi-card__label">Page views (7d)</p>
        <p class="kpi-card__value">{{ number_format($pageViews ?? 8420) }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Add to cart rate</p>
        <p class="kpi-card__value">{{ $cartRate ?? '8.2' }}%</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Avg. order value</p>
        <p class="kpi-card__value">${{ number_format($aov ?? 112, 0) }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Return rate</p>
        <p class="kpi-card__value">{{ $returnRate ?? '1.8' }}%</p>
    </div>
</div>

<div class="admin-charts">
    <div class="admin-chart-card">
        <h2 class="admin-chart-card__title">Monthly revenue</h2>
        <canvas id="chart-sales"
            data-labels='@json($salesLabels ?? ["Jan","Feb","Mar","Apr","May","Jun"])'
            data-values='@json($salesValues ?? [8200,9100,7800,10200,11500,12450])'>
        </canvas>
    </div>
    <div class="admin-chart-card">
        <h2 class="admin-chart-card__title">Weekly orders</h2>
        <canvas id="chart-orders"
            data-labels='@json($ordersLabels ?? ["W1","W2","W3","W4"])'
            data-values='@json($ordersValues ?? [18,22,25,21])'>
        </canvas>
    </div>
</div>

<div class="card" style="margin-top:var(--space-xl);">
    <h2 class="card__title" style="margin-bottom:var(--space-md);">Top products</h2>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Views</th>
                    <th>Sales</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($topProducts ?? [
                    ['name' => 'Denim Jacket 80s', 'views' => 420, 'sales' => 8, 'revenue' => 712],
                    ['name' => 'Floral Dress 70s', 'views' => 380, 'sales' => 6, 'revenue' => 720],
                ] as $p)
                    <tr>
                        <td>{{ $p['name'] }}</td>
                        <td>{{ $p['views'] }}</td>
                        <td>{{ $p['sales'] }}</td>
                        <td>${{ number_format($p['revenue'], 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
