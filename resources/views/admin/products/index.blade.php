@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products')
@section('page-subtitle', 'Manage catalog items')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">All products</h2>
        <p class="admin-page-header__subtitle">{{ $products->total() }} items in catalog</p>
    </div>
    @if (Route::has('admin.products.create'))
        <a href="{{ route('admin.products.create') }}" class="btn btn--primary">Add product</a>
    @endif
</div>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Verified</th>
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
                            <span class="badge badge--warning">Low stock</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge--{{ $product->status->value === 'published' ? 'success' : 'warning' }}">
                            {{ ucfirst(str_replace('_', ' ', $product->status->value)) }}
                        </span>
                    </td>
                    <td>
                        @if ($product->is_authenticated)
                            <span class="badge badge--success">Verified</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="table__actions">
                            @if (Route::has('admin.products.edit'))
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn--ghost btn--sm">Edit</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No products yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($products->hasPages())
    <div style="margin-top:var(--space-lg);">
        {{ $products->links() }}
    </div>
@endif
@endsection
