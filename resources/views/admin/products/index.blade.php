@extends('layouts.admin')

@section('title', 'Productos')
@section('page-title', 'Productos')
@section('page-subtitle', 'Administra los artículos del catálogo')

@section('content')
@php
    $statusLabels = ['draft' => 'Borrador', 'published' => 'Publicado', 'archived' => 'Archivado', 'sold_out' => 'Agotado'];
@endphp
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Todos los productos</h2>
        <p class="admin-page-header__subtitle">{{ $products->total() }} artículos en el catálogo</p>
    </div>
    @if (Route::has('admin.products.create'))
        <a href="{{ route('admin.products.create') }}" class="btn btn--primary">Agregar producto</a>
    @endif
</div>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
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
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Aún no hay productos.</td>
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
@endsection
