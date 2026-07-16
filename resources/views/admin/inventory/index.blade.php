@extends('layouts.admin')

@section('title', 'Inventory')
@section('page-title', 'Inventory')
@section('page-subtitle', 'Stock levels and movements')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Stock overview</h2>
        <p class="admin-page-header__subtitle">{{ $lowStockCount ?? '—' }} items below threshold</p>
    </div>
</div>

<div class="kpi-grid" style="margin-bottom:var(--space-xl);">
    <div class="kpi-card">
        <p class="kpi-card__label">Total SKUs</p>
        <p class="kpi-card__value">{{ $totalSkus ?? '—' }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">In stock</p>
        <p class="kpi-card__value">{{ $inStock ?? '—' }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Out of stock</p>
        <p class="kpi-card__value">{{ $outOfStock ?? '—' }}</p>
    </div>
</div>

<div class="card" style="margin-bottom:var(--space-xl);">
    <div class="card__header">
        <h2 class="card__title">Adjust stock</h2>
    </div>
    <form method="POST" action="{{ route('admin.inventory.store') }}" style="display:grid;gap:var(--space-md);max-width:32rem;">
        @csrf
        @if ($errors->any())
            <div class="flash flash--error" role="alert">
                <ul style="margin:0;padding-left:1.25rem;">
                    @foreach ($errors->all() as $errorMessage)
                        <li>{{ $errorMessage }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="form-group">
            <label class="form-label" for="product_id">Product</label>
            <select id="product_id" name="product_id" class="form-select" required>
                <option value="">Select product</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" @selected(old('product_id') === $p->id)>{{ $p->name }} ({{ $p->sku }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-row form-row--2">
            <div class="form-group" id="quantity-group">
                <label class="form-label" for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" class="form-input" min="1" value="{{ old('quantity') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="type">Type</label>
                <select id="type" name="type" class="form-select" required>
                    <option value="stock_in" @selected(old('type') === 'stock_in')>Stock in</option>
                    <option value="stock_out" @selected(old('type') === 'stock_out')>Stock out</option>
                    <option value="adjust" @selected(old('type') === 'adjust')>Adjustment</option>
                </select>
            </div>
        </div>
        <div class="form-group" id="new-quantity-group" style="display:none;">
            <label class="form-label" for="new_quantity_available">New available quantity</label>
            <input type="number" id="new_quantity_available" name="new_quantity_available" class="form-input" min="0" value="{{ old('new_quantity_available') }}">
            <p class="text-muted" style="margin-top:var(--space-xs);font-size:0.875rem;">Sets the product stock to this exact value.</p>
        </div>
        <div class="form-group">
            <label class="form-label" for="notes">Notes</label>
            <input type="text" id="notes" name="notes" class="form-input" value="{{ old('notes') }}">
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
                @forelse ($movements as $movement)
                    @php
                        $typeValue = $movement->type->value;
                        $badgeClass = match ($typeValue) {
                            'stock_in', 'return' => 'success',
                            'stock_out' => 'warning',
                            default => 'primary',
                        };
                        $qty = (int) $movement->quantity;
                    @endphp
                    <tr>
                        <td>{{ $movement->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td>{{ $movement->product?->name ?? '—' }}</td>
                        <td><span class="badge badge--{{ $badgeClass }}">{{ str_replace('_', ' ', strtoupper($typeValue)) }}</span></td>
                        <td>{{ $qty > 0 ? '+' : '' }}{{ $qty }}</td>
                        <td>{{ $movement->user?->name ?? '—' }}</td>
                        <td>{{ $movement->notes ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No movements recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($movements->hasPages())
        <div style="margin-top:var(--space-lg);">
            {{ $movements->links() }}
        </div>
    @endif
</div>

<script>
    (function () {
        var typeSelect = document.getElementById('type');
        var quantityGroup = document.getElementById('quantity-group');
        var newQuantityGroup = document.getElementById('new-quantity-group');
        var quantityInput = document.getElementById('quantity');
        var newQuantityInput = document.getElementById('new_quantity_available');

        function syncAdjustFields() {
            var isAdjust = typeSelect.value === 'adjust';
            quantityGroup.style.display = isAdjust ? 'none' : '';
            newQuantityGroup.style.display = isAdjust ? '' : 'none';
            quantityInput.required = !isAdjust;
            newQuantityInput.required = isAdjust;
        }

        typeSelect.addEventListener('change', syncAdjustFields);
        syncAdjustFields();
    })();
</script>
@endsection
