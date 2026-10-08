@extends('layouts.admin')

@section('title', 'Analítica')
@section('page-title', 'Analítica')
@section('page-subtitle', 'Informes de ventas y tráfico')

@section('content')
<form method="GET" action="{{ route('admin.analytics.index') }}" class="admin-filters">
    <label class="form-group">
        <span class="form-label">Desde</span>
        <input type="date" name="from" class="form-input @error('from') form-input--error @enderror" value="{{ old('from', $from->toDateString()) }}" @error('from') aria-invalid="true" aria-describedby="from-error" @enderror>
        @error('from')<span class="form-error" id="from-error">{{ $message }}</span>@enderror
    </label>
    <label class="form-group">
        <span class="form-label">Hasta</span>
        <input type="date" name="to" class="form-input @error('to') form-input--error @enderror" value="{{ old('to', $to->toDateString()) }}" @error('to') aria-invalid="true" aria-describedby="to-error" @enderror>
        @error('to')<span class="form-error" id="to-error">{{ $message }}</span>@enderror
    </label>
    <button type="submit" class="btn btn--primary btn--sm">Aplicar</button>
</form>

<div class="kpi-grid">
    <div class="kpi-card">
        <p class="kpi-card__label">Vistas de productos</p>
        <p class="kpi-card__value">{{ number_format($pageViews ?? $kpis['product_views'] ?? 0) }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Tasa de conversión</p>
        <p class="kpi-card__value">{{ number_format((float) ($cartRate ?? $kpis['conversion_rate'] ?? 0), 1) }}%</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Valor promedio del pedido</p>
        <p class="kpi-card__value">${{ number_format((float) ($aov ?? $kpis['average_order_value'] ?? 0), 0) }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Bajo / agotado</p>
        <p class="kpi-card__value">{{ ($lowStockCount ?? 0) }}/{{ ($outOfStockCount ?? 0) }}</p>
    </div>
</div>

<div class="admin-charts">
    <div class="admin-chart-card">
        <h2 class="admin-chart-card__title">Ingresos diarios</h2>
        <canvas class="admin-chart-card__canvas" id="chart-sales"
            data-labels='@json($salesLabels ?? [])'
            data-values='@json($salesValues ?? [])'>
        </canvas>
    </div>
    <div class="admin-chart-card">
        <h2 class="admin-chart-card__title">Pedidos diarios</h2>
        <canvas class="admin-chart-card__canvas" id="chart-orders"
            data-labels='@json($ordersLabels ?? [])'
            data-values='@json($ordersValues ?? [])'>
        </canvas>
    </div>
</div>

<div class="card admin-panel admin-panel--top-spaced">
    <h2 class="card__title admin-form__title">Productos principales</h2>
    <div class="table-wrap">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Vistas</th>
                    <th>Ventas</th>
                    <th>Ingresos</th>
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
                        <td colspan="4">No hay actividad de productos en este período.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
