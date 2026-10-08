@extends('layouts.admin')

@section('title', 'Productos')
@section('page-title', 'Productos')
@section('page-subtitle', 'Administra los artículos del catálogo')

@section('content')
@php
    $statusLabels = ['draft' => 'Borrador', 'published' => 'Publicado', 'archived' => 'Archivado', 'sold_out' => 'Agotado'];
@endphp
<div class="admin-products" x-data="{ selected: [], all: false }">
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Todos los productos</h2>
        <p class="admin-page-header__subtitle">{{ $products->total() }} artículos en el catálogo</p>
    </div>
    @if (Route::has('admin.products.create'))
        <a href="{{ route('admin.products.create') }}" class="btn btn--primary">Agregar producto</a>
    @endif
</div>

<form method="POST" action="{{ route('admin.products.bulk-status') }}" class="admin-bulk-actions" x-on:submit="if (!selected.length || !confirm('¿Cambiar el estado de los productos seleccionados?')) $event.preventDefault()">
    @csrf
    @method('PATCH')
    <template x-for="id in selected" :key="id"><input type="hidden" name="product_ids[]" :value="id"></template>
    <input type="hidden" name="page" value="{{ request('page', 1) }}">
    <label class="form-label" for="bulk-status">Acción masiva</label>
    <select id="bulk-status" name="status" class="form-select">
        <option value="published">Publicar</option>
        <option value="draft">Pasar a borrador</option>
    </select>
    <button type="submit" class="btn btn--ghost btn--sm">Aplicar</button>
</form>
<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th><input type="checkbox" aria-label="Seleccionar todos" x-model="all" x-on:change="selected = all ? {{ $products->pluck('id')->values()->toJson() }} : []"></th>
                <th>SKU</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Inventario</th>
                <th>Estado</th>
                <th>Autenticado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td><input type="checkbox" value="{{ $product->id }}" x-model="selected" aria-label="Seleccionar {{ $product->name }}"></td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category?->name ?? '—' }}</td>
                    <td>${{ number_format((float) $product->price, 2) }}</td>
                    <td>
                        {{ $product->quantity_available }}
                        @if ($product->quantity_available <= $product->low_stock_threshold)
                            <span class="badge badge--warning">Poco inventario</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge--{{ $product->status->value === 'published' ? 'success' : 'warning' }}">
                            {{ $statusLabels[$product->status->value] ?? $product->status->value }}
                        </span>
                    </td>
                    <td>
                        @if ($product->is_authenticated)
                            <span class="badge badge--success">Autenticado</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="admin-table__actions">
                            @if (Route::has('admin.products.edit'))
                                <a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.products.edit', $product) }}">Editar</a>
                                <form method="POST" action="{{ route('admin.products.duplicate', $product) }}" x-on:submit="if (!confirm('¿Duplicar este producto?')) $event.preventDefault()">
                                    @csrf
                                    <button type="submit" class="btn btn--ghost btn--sm">Duplicar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Aún no hay productos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($products->hasPages())
    <div class="admin-pagination">
        {{ $products->links() }}
    </div>
@endif
</div>
@endsection
