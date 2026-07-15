@extends('layouts.admin')

@section('title', 'Inventory')
@section('page-title', 'Inventory')
@section('page-subtitle', 'Stock levels and movements')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Stock overview</h2>
        <p class="admin-page-header__subtitle">{{ $lowStockCount ?? 5 }} items below threshold</p>
    </div>
</div>

<div class="kpi-grid" style="margin-bottom:var(--space-xl);">
    <div class="kpi-card">
        <p class="kpi-card__label">Total SKUs</p>
        <p class="kpi-card__value">{{ $totalSkus ?? 124 }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">In stock</p>
        <p class="kpi-card__value">{{ $inStock ?? 98 }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Out of stock</p>
        <p class="kpi-card__value">{{ $outOfStock ?? 21 }}</p>
    </div>
</div>

<div class="card" style="margin-bottom:var(--space-xl);">
    <div class="card__header">
        <h2 class="card__title">Adjust stock</h2>
    </div>
    <form method="POST" action="{{ Route::has('admin.inventory.adjust') ? route('admin.inventory.adjust') : '#' }}" style="display:grid;gap:var(--space-md);max-width:32rem;">
        @csrf
        <div class="form-group">
            <label class="form-label" for="product_id">Product</label>
            <select id="product_id" name="product_id" class="form-select" required>
                <option value="">Select product</option>
                @foreach ($products ?? [] as $p)
                    <option value="{{ $p->id ?? $p['id'] }}">{{ $p->name ?? $p['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-row form-row--2">
            <div class="form-group">
                <label class="form-label" for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="type">Type</label>
                <select id="type" name="type" class="form-select">
                    <option value="in">Stock in</option>
                    <option value="out">Stock out</option>
                    <option value="adjustment">Adjustment</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="notes">Notes</label>
            <input type="text" id="notes" name="notes" class="form-input">
        </div>
        <button type="submit" class="btn btn--primary" style="width:fit-content;">Record movement</button>
    </form>
</div>

<div class="card">
    <div class="card__header">
        <h2 class="card__title">Movement log</h2>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th>User</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movements ?? [
                    ['date' => '2026-07-14', 'product' => 'Denim Jacket 80s', 'type' => 'out', 'qty' => -1, 'user' => 'Admin', 'notes' => 'Order LC-1042'],
                    ['date' => '2026-07-12', 'product' => 'Floral Dress 70s', 'type' => 'in', 'qty' => 1, 'user' => 'Staff', 'notes' => 'New arrival'],
                ] as $m)
                    <tr>
                        <td>{{ $m['date'] }}</td>
                        <td>{{ $m['product'] }}</td>
                        <td><span class="badge badge--{{ $m['type'] === 'in' ? 'success' : 'warning' }}">{{ strtoupper($m['type']) }}</span></td>
                        <td>{{ $m['qty'] > 0 ? '+' : '' }}{{ $m['qty'] }}</td>
                        <td>{{ $m['user'] }}</td>
                        <td>{{ $m['notes'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
