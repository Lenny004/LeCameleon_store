@extends('layouts.admin')

@section('title', $brand->name . ' — Brands')
@section('page-title', $brand->name)

@section('content')
<div class="card" style="max-width:40rem;">
    <p><strong>Slug:</strong> {{ $brand->slug }}</p>
    <p><strong>Products:</strong> {{ $brand->products_count }}</p>
    @if ($brand->description)
        <p><strong>Description:</strong> {{ $brand->description }}</p>
    @endif
    <div style="margin-top:var(--space-lg);display:flex;gap:var(--space-md);">
        <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn--primary">Edit</a>
        <a href="{{ route('admin.brands.index') }}" class="btn btn--ghost">Back</a>
    </div>
</div>
@endsection
