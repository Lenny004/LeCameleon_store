@extends('layouts.admin')

@section('title', $brand->name . ' — Brands')
@section('page-title', $brand->name)

@section('content')
<div class="card admin-panel admin-panel--narrow">
    <p><strong>Slug:</strong> {{ $brand->slug }}</p>
    <p><strong>Products:</strong> {{ $brand->products_count }}</p>
    @if ($brand->description)
        <p><strong>Description:</strong> {{ $brand->description }}</p>
    @endif
    <div class="admin-actions admin-actions--spaced">
        <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn--primary">Edit</a>
        <a href="{{ route('admin.brands.index') }}" class="btn btn--ghost">Back</a>
    </div>
</div>
@endsection
