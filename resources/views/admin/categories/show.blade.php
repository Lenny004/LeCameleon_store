@extends('layouts.admin')

@section('title', $category->name . ' — Categorías')
@section('page-title', $category->name)

@section('content')
<div class="card admin-panel admin-panel--narrow">
    <p><strong>Identificador:</strong> {{ $category->slug }}</p>
    <p><strong>Superior:</strong> {{ $category->parent?->name ?? '—' }}</p>
    <p><strong>Estado:</strong> <span class="badge badge--{{ $category->is_active ? 'success' : 'warning' }}">{{ $category->is_active ? 'Activa' : 'Inactiva' }}</span></p>
    <p><strong>Orden:</strong> {{ $category->sort_order }}</p>
    @if ($category->description)
        <p><strong>Descripción:</strong> {{ $category->description }}</p>
    @endif
    @if ($category->children->isNotEmpty())
        <p><strong>Subcategorías:</strong> {{ $category->children->pluck('name')->join(', ') }}</p>
    @endif
    <div class="admin-actions admin-actions--spaced">
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn--primary">Editar</a>
        <a href="{{ route('admin.categories.index') }}" class="btn btn--ghost">Volver</a>
    </div>
</div>
@endsection
