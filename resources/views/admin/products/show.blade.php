@extends('layouts.admin')

@section('title', ($product->name ?? 'Product') . ' — Products')
@section('page-title', $product->name ?? 'Product')

@section('content')
<div class="card" style="max-width:40rem;">
    <p><strong>SKU:</strong> {{ $product->sku }}</p>
    <p><strong>Status:</strong> {{ $product->status?->value ?? $product->status }}</p>
    <p><strong>Price:</strong> {{ number_format((float) $product->price, 2) }}</p>
    <p><strong>Available:</strong> {{ $product->quantity_available }}</p>
    <p><strong>Reserved:</strong> {{ $product->quantity_reserved }}</p>
    <div style="margin-top:var(--space-lg);display:flex;gap:var(--space-md);">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn--primary">Edit</a>
        <a href="{{ route('admin.products.index') }}" class="btn btn--ghost">Back</a>
    </div>
</div>
@endsection
