@extends('layouts.admin')

@section('title', 'Inventario')
@section('page-title', 'Inventario')
@section('page-subtitle', 'Niveles y movimientos de existencias')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Resumen de inventario</h2>
        <p class="admin-page-header__subtitle">{{ $lowStockCount }} productos por debajo del umbral</p>
    </div>
</div>

<div class="kpi-grid admin-panel admin-panel--spaced">
    <div class="kpi-card">
        <p class="kpi-card__label">SKU totales</p>
        <p class="kpi-card__value">{{ $totalSkus }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">En existencia</p>
        <p class="kpi-card__value">{{ $inStock }}</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-card__label">Agotados</p>
        <p class="kpi-card__value">{{ $outOfStock }}</p>
    </div>
</div>

<div class="card admin-panel admin-panel--spaced">
    <div class="card__header">
        <h2 class="card__title">Existencias actuales</h2>
    </div>
    <div class="table-wrap">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Producto</th>
                    <th>Marca</th>
                    <th>Categoría</th>
                    <th>Disponible</th>
                    <th>Reservado</th>
                    <th>Vendible</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stockProducts as $product)
                    @php $sellable = $product->quantity_available - $product->quantity_reserved; @endphp
                    <tr>
                        <td><code>{{ $product->sku }}</code></td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->brand?->name ?? '—' }}</td>
                        <td>{{ $product->category?->name ?? '—' }}</td>
                        <td>{{ $product->quantity_available }}</td>
                        <td>{{ $product->quantity_reserved }}</td>
                        <td><span class="badge badge--{{ $sellable > 0 ? ($sellable <= $product->low_stock_threshold ? 'warning' : 'success') : 'danger' }}">{{ $sellable }}</span></td>
                        <td><a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.products.edit', $product) }}">Editar producto</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-muted">No se encontraron productos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($stockProducts->hasPages())
        <div class="admin-pagination">{{ $stockProducts->links() }}</div>
    @endif
</div>

<div class="card admin-panel admin-panel--spaced">
    <div class="card__header">
        <h2 class="card__title">Ajustar existencias</h2>
    </div>
    <form method="POST" action="{{ route('admin.inventory.store') }}" class="admin-form admin-form--inventory">
        @csrf
        <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
        @if ($errors->any())
            <div class="flash flash--error" role="alert">
                <ul class="admin-form__list">
                    @foreach ($errors->all() as $errorMessage)
                        <li>{{ $errorMessage }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="form-group">
            <label class="form-label" for="product_id">Producto <span class="form-label__required" aria-hidden="true">*</span></label>
            <select id="product_id" name="product_id" class="form-select @error('product_id') form-select--error @enderror" required @error('product_id') aria-invalid="true" aria-describedby="product_id-error" @enderror>
                <option value="" disabled {{ old('product_id') ? '' : 'selected' }}>Selecciona un producto</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" @selected(old('product_id') === $p->id)>{{ $p->name }} ({{ $p->sku }})</option>
                @endforeach
            </select>
            @error('product_id')<span class="form-error" id="product_id-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-row form-row--cols-2">
            <div class="form-group" id="quantity-group">
                <label class="form-label" for="quantity">Cantidad <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="number" id="quantity" name="quantity" class="form-input @error('quantity') form-input--error @enderror" min="1" max="2147483647" step="1" inputmode="numeric" placeholder="1" value="{{ old('quantity') }}" required @error('quantity') aria-invalid="true" aria-describedby="quantity-error" @enderror>
                @error('quantity')<span class="form-error" id="quantity-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="type">Tipo <span class="form-label__required" aria-hidden="true">*</span></label>
                <select id="type" name="type" class="form-select @error('type') form-select--error @enderror" required @error('type') aria-invalid="true" aria-describedby="type-error" @enderror>
                    <option value="" disabled {{ old('type') ? '' : 'selected' }}>Selecciona un tipo</option>
                    <option value="stock_in" @selected(old('type') === 'stock_in')>Entrada</option>
                    <option value="stock_out" @selected(old('type') === 'stock_out')>Salida</option>
                    <option value="adjust" @selected(old('type') === 'adjust')>Ajuste</option>
                </select>
                @error('type')<span class="form-error" id="type-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="form-group is-hidden" id="new-quantity-group">
            <label class="form-label" for="new_quantity_available">Nueva cantidad disponible <span class="form-label__required" aria-hidden="true">*</span></label>
            <input type="number" id="new_quantity_available" name="new_quantity_available" class="form-input @error('new_quantity_available') form-input--error @enderror" min="0" max="2147483647" step="1" inputmode="numeric" placeholder="0" value="{{ old('new_quantity_available') }}" @error('new_quantity_available') aria-invalid="true" aria-describedby="new_quantity_available-error" @enderror>
            @error('new_quantity_available')<span class="form-error" id="new_quantity_available-error">{{ $message }}</span>@enderror
            <p class="text-muted admin-form__hint">Establece las existencias del producto en este valor exacto.</p>
        </div>
        <div class="form-group">
            <label class="form-label" for="notes">Notas</label>
            <input type="text" id="notes" name="notes" class="form-input @error('notes') form-input--error @enderror" maxlength="500" placeholder="Motivo del movimiento" value="{{ old('notes') }}" @error('notes') aria-invalid="true" aria-describedby="notes-error" @enderror>
            @error('notes')<span class="form-error" id="notes-error">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="btn btn--primary admin-form__submit">Registrar movimiento</button>
    </form>
</div>

<div class="card">
    <div class="card__header">
        <h2 class="card__title">Movement log</h2>
    </div>
    <div class="table-wrap">
        <table class="table admin-table">
            <thead>
                <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                    <th>Qty</th>
                        <th>Usuario</th>
                        <th>Notas</th>
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
                        <td colspan="6">Aún no hay movimientos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($movements->hasPages())
        <div class="admin-pagination">
            {{ $movements->links() }}
        </div>
    @endif
</div>

<script nonce="{{ Vite::cspNonce() }}">
    (function () {
        var typeSelect = document.getElementById('type');
        var quantityGroup = document.getElementById('quantity-group');
        var newQuantityGroup = document.getElementById('new-quantity-group');
        var quantityInput = document.getElementById('quantity');
        var newQuantityInput = document.getElementById('new_quantity_available');

        function syncAdjustFields() {
            var isAdjust = typeSelect.value === 'adjust';
            quantityGroup.classList.toggle('is-hidden', isAdjust);
            newQuantityGroup.classList.toggle('is-hidden', !isAdjust);
            quantityInput.required = !isAdjust;
            newQuantityInput.required = isAdjust;
        }

        typeSelect.addEventListener('change', syncAdjustFields);
        syncAdjustFields();
    })();
</script>
@endsection
