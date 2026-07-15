@extends('layouts.admin')

@section('title', $category->name . ' — Categories')
@section('page-title', $category->name)

@section('content')
<div class="card" style="max-width:40rem;">
    <p><strong>Slug:</strong> {{ $category->slug }}</p>
    <p><strong>Parent:</strong> {{ $category->parent?->name ?? '—' }}</p>
    <p><strong>Status:</strong> <span class="badge badge--{{ $category->is_active ? 'success' : 'warning' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></p>
    <p><strong>Sort order:</strong> {{ $category->sort_order }}</p>
    @if ($category->description)
        <p><strong>Description:</strong> {{ $category->description }}</p>
    @endif
    @if ($category->children->isNotEmpty())
        <p><strong>Subcategories:</strong> {{ $category->children->pluck('name')->join(', ') }}</p>
    @endif
    <div style="margin-top:var(--space-lg);display:flex;gap:var(--space-md);">
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn--primary">Edit</a>
        <a href="{{ route('admin.categories.index') }}" class="btn btn--ghost">Back</a>
    </div>
</div>
@endsection
