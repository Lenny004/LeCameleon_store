@extends('layouts.admin')

@section('title', ($product->name ?? 'Product') . ' — Products')
@section('page-title', $product->name ?? 'Product')

@section('content')
@php
    $approvedReviews = $product->reviews->where('is_approved', true);
    $avgRating = $approvedReviews->avg('rating');
@endphp
<div class="card" style="max-width:40rem;">
    <p><strong>SKU:</strong> {{ $product->sku }}</p>
    <p><strong>Status:</strong> {{ $product->status?->value ?? $product->status }}</p>
    <p><strong>Price:</strong> {{ number_format((float) $product->price, 2) }}</p>
    <p><strong>Available:</strong> {{ $product->quantity_available }}</p>
    <p><strong>Reserved:</strong> {{ $product->quantity_reserved }}</p>
    <p>
        <strong>Rating:</strong>
        @if ($approvedReviews->isNotEmpty())
            {{ number_format((float) $avgRating, 1) }} / 5
            ({{ $approvedReviews->count() }} approved)
        @else
            No approved reviews yet
        @endif
        —
        <a href="{{ route('admin.reviews.index', ['q' => $product->name]) }}">Moderate reviews</a>
    </p>
    <div style="margin-top:var(--space-lg);display:flex;gap:var(--space-md);">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn--primary">Edit</a>
        <a href="{{ route('admin.products.index') }}" class="btn btn--ghost">Back</a>
    </div>
</div>
@endsection
