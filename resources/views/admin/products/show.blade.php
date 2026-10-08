@extends('layouts.admin')

@section('title', ($product->name ?? 'Producto') . ' — Productos')
@section('page-title', $product->name ?? 'Producto')

@section('content')
@php
    $approvedReviews = $product->reviews->where('is_approved', true);
    $avgRating = $approvedReviews->avg('rating');
@endphp
<div class="card admin-panel admin-panel--narrow">
    <p><strong>SKU:</strong> {{ $product->sku }}</p>
    <p><strong>Estado:</strong> {{ $product->status?->value ?? $product->status }}</p>
    <p><strong>Precio:</strong> {{ number_format((float) $product->price, 2) }}</p>
    <p><strong>Disponible:</strong> {{ $product->quantity_available }}</p>
    <p><strong>Reservado:</strong> {{ $product->quantity_reserved }}</p>
    <p>
        <strong>Calificación:</strong>
        @if ($approvedReviews->isNotEmpty())
            {{ number_format((float) $avgRating, 1) }} / 5
            ({{ $approvedReviews->count() }} aprobadas)
        @else
            Aún no hay reseñas aprobadas
        @endif
        —
        <a href="{{ route('admin.reviews.index', ['q' => $product->name]) }}">Moderar reseñas</a>
    </p>
    <div class="admin-actions admin-actions--spaced">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn--primary">Editar</a>
        <a href="{{ route('admin.products.index') }}" class="btn btn--ghost">Volver</a>
    </div>
</div>
@endsection
