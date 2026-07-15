@extends('layouts.admin')

@section('title', 'Analytics')
@section('page-title', 'Analytics')
@section('page-subtitle', 'Sales and traffic reports')

@section('content')
<form method="GET" action="{{ route('admin.analytics.index') }}" class="admin-filters" style="display:flex;flex-wrap:wrap;gap:var(--space-sm);margin-bottom:var(--space-xl);align-items:end;">
    <label>
        <span class="form-label">From</span>
        <input type="date" name="from" class="form-input" value="{{ $from->toDateString() }}">
    </label>
    <label>
        <span class="form-label">To</span>
        <input type="date" name="to" class="form-input" value="{{ $to->toDateString() }}">
    </label>
    <button type="submit" class="btn btn--primary btn--sm">Apply</button>
</form>

<div class="kpi-grid">
    <div class="kpi-card">
        <p class="kpi-card__label">Product views</p>
        <p class="kpi-card__value">{{ number_format($pageViews ?? $kpis['product_views'] ?? 0) }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Conversion rate</p>
        <p class="kpi-card__value">{{ number_format((float) ($cartRate ?? $kpis['conversion_rate'] ?? 0), 1) }}%</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Avg. order value</p>
        <p class="kpi-card__value">${{ number_format((float) ($aov ?? $kpis['average_order_value'] ?? 0), 0) }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Low / out of stock</p>
        <p class="kpi-card__value">{{ ($lowStockCount ?? 0) }}/{{ ($outOfStockCount ?? 0) }}</p>
    </div>
</div>

<div class="admin-charts">
    <div class="admin-chart-card">
        <h2 class="admin-chart-card__title">Daily revenue</h2>
        <canvas id="chart-sales"
            data-labels='@json($salesLabels ?? [])'
            data-values='@json($salesValues ?? [])'>
        </canvas>
    </div>
    <div class="admin-chart-card">
        <h2 class="admin-chart-card__title">Daily orders</h2>
        <canvas id="chart-orders"
            data-labels='@json($ordersLabels ?? [])'
            data-values='@json($ordersValues ?? [])'>
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
                @forelse ($topProducts ?? [] as $p)
                    <tr>
                        <td>{{ is_array($p) ? $p['name'] : ($p->name ?? '—') }}</td>
                        <td>{{ is_array($p) ? $p['views'] : ($p->views ?? 0) }}</td>
                        <td>{{ is_array($p) ? $p['sales'] : ($p->sales ?? 0) }}</td>
                        <td>${{ number_format((float) (is_array($p) ? $p['revenue'] : ($p->revenue ?? 0)), 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No product activity in this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
