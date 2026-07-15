@extends('layouts.admin')

@section('title', 'Order ' . ($order->id ?? ''))
@section('page-title', 'Order #' . ($order->id ?? 'LC-1042'))

@section('content')
@php
    $order = $order ?? (object) [
        'id' => 'LC-1042',
        'customer' => 'maria@email.com',
        'status' => 'processing',
        'total' => 189.00,
        'shipping_address' => 'Calle Ejemplo 123',
        'items' => [
            (object) ['name' => 'Denim Jacket 80s', 'sku' => 'LC-001', 'qty' => 1, 'price' => 89],
            (object) ['name' => 'Floral Dress 70s', 'sku' => 'LC-002', 'qty' => 1, 'price' => 100],
        ],
    ];
@endphp

<div style="display:grid;gap:var(--space-xl);max-width:48rem;">
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Order details</h2>
            <span class="badge badge--primary">{{ ucfirst($order->status) }}</span>
        </div>
        <p class="text-muted">Customer: {{ $order->customer }}</p>
        <p class="text-muted" style="margin-top:var(--space-xs);">Ship to: {{ $order->shipping_address }}</p>
    </div>

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-md);">Line items</h2>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->sku }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p style="text-align:right;margin-top:var(--space-md);font-weight:700;">Total: ${{ number_format($order->total, 2) }}</p>
    </div>

    @if (Route::has('admin.orders.update'))
        <form method="POST" action="{{ route('admin.orders.update', $order->id) }}" class="card">
            @csrf
            @method('PATCH')
            <h2 class="card__title" style="margin-bottom:var(--space-md);">Update status</h2>
            <div class="form-group">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select">
                    @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ ($order->status ?? '') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn--primary">Update order</button>
        </form>
    @endif
</div>
@endsection
