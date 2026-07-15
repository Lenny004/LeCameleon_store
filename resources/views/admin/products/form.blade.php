@extends('layouts.admin')

@section('title', ($product->name ?? 'New product') . ' — Products')
@section('page-title', isset($product) ? 'Edit product' : 'New product')

@section('content')
<form method="POST" action="{{ isset($product) && Route::has('admin.products.update') ? route('admin.products.update', $product->id ?? $product->sku) : (Route::has('admin.products.store') ? route('admin.products.store') : '#') }}" style="max-width:40rem;display:flex;flex-direction:column;gap:var(--space-xl);">
    @csrf
    @if (isset($product))
        @method('PUT')
    @endif

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-lg);">Basic info</h2>
        <div style="display:flex;flex-direction:column;gap:var(--space-md);">
            <div class="form-group">
                <label class="form-label" for="name">Name</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $product->name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="slug">Slug</label>
                <input type="text" id="slug" name="slug" class="form-input" value="{{ old('slug', $product->slug ?? '') }}">
            </div>
            <div class="form-row form-row--2">
                <div class="form-group">
                    <label class="form-label" for="price">Price</label>
                    <input type="number" id="price" name="price" class="form-input" step="0.01" value="{{ old('price', $product->price ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="stock">Stock</label>
                    <input type="number" id="stock" name="stock" class="form-input" value="{{ old('stock', $product->stock ?? 1) }}" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-textarea">{{ old('description', $product->description ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-lg);">Attributes</h2>
        <div class="form-row form-row--2">
            <div class="form-group">
                <label class="form-label" for="era">Era</label>
                <select id="era" name="era" class="form-select">
                    @foreach (['1960s', '1970s', '1980s', '1990s', '2000s'] as $era)
                        <option value="{{ $era }}" {{ old('era', $product->era ?? '') === $era ? 'selected' : '' }}>{{ $era }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="condition">Condition</label>
                <select id="condition" name="condition" class="form-select">
                    @foreach (['Excellent', 'Very good', 'Good', 'Fair'] as $cond)
                        <option value="{{ $cond }}" {{ old('condition', $product->condition ?? '') === $cond ? 'selected' : '' }}>{{ $cond }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:var(--space-sm);">
        <button type="submit" class="btn btn--primary">Save product</button>
        @if (Route::has('admin.products.index'))
            <a href="{{ route('admin.products.index') }}" class="btn btn--ghost">Cancel</a>
        @endif
    </div>
</form>
@endsection
