@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products')
@section('page-subtitle', 'Manage catalog items')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">All products</h2>
        <p class="admin-page-header__subtitle">{{ count($products ?? []) ?: 24 }} items in catalog</p>
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
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products ?? [
                ['sku' => 'LC-001', 'name' => 'Denim Jacket 80s', 'category' => 'Clothing', 'price' => 89, 'stock' => 1, 'status' => 'Active'],
                ['sku' => 'LC-002', 'name' => 'Floral Dress 70s', 'category' => 'Clothing', 'price' => 120, 'stock' => 1, 'status' => 'Active'],
                ['sku' => 'LC-003', 'name' => 'Leather Bag Vintage', 'category' => 'Accessories', 'price' => 65, 'stock' => 0, 'status' => 'Sold out'],
            ] as $product)
                <tr>
                    <td>{{ $product['sku'] }}</td>
                    <td>{{ $product['name'] }}</td>
                    <td>{{ $product['category'] }}</td>
                    <td>${{ number_format($product['price'], 2) }}</td>
                    <td>{{ $product['stock'] }}</td>
                    <td>
                        <span class="badge badge--{{ $product['status'] === 'Active' ? 'success' : 'warning' }}">{{ $product['status'] }}</span>
                    </td>
                    <td>
                        <div class="table__actions">
                            @if (Route::has('admin.products.edit'))
                                <a href="{{ route('admin.products.edit', $product['sku']) }}" class="btn btn--ghost btn--sm">Edit</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
