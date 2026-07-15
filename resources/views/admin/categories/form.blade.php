@extends('layouts.admin')

@section('title', ($category->name ?? 'New category') . ' — Categories')
@section('page-title', isset($category) ? 'Edit category' : 'New category')

@section('content')
<form method="POST" action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" style="max-width:40rem;display:flex;flex-direction:column;gap:var(--space-xl);">
    @csrf
    @if (isset($category))
        @method('PUT')
    @endif

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-lg);">Category info</h2>
        <div style="display:flex;flex-direction:column;gap:var(--space-md);">
            <div class="form-group">
                <label class="form-label" for="parent_id">Parent category</label>
                <select id="parent_id" name="parent_id" class="form-select">
                    <option value="">— None —</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" {{ (string) old('parent_id', $category->parent_id ?? '') === (string) $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="name">Name</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $category->name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="slug">Slug</label>
                <input type="text" id="slug" name="slug" class="form-input" value="{{ old('slug', $category->slug ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-textarea">{{ old('description', $category->description ?? '') }}</textarea>
            </div>
            <div class="form-row form-row--2">
                <div class="form-group">
                    <label class="form-label" for="sort_order">Sort order</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-input" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                        Active
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:var(--space-sm);">
        <button type="submit" class="btn btn--primary">{{ isset($category) ? 'Save category' : 'Create category' }}</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn--ghost">Cancel</a>
    </div>
</form>
@endsection
