@extends('layouts.admin')

@section('title', ($brand->name ?? 'New brand') . ' — Brands')
@section('page-title', isset($brand) ? 'Edit brand' : 'New brand')

@section('content')
<form method="POST" action="{{ isset($brand) ? route('admin.brands.update', $brand) : route('admin.brands.store') }}" class="admin-form admin-form--narrow">
    @csrf
    @if (isset($brand))
        @method('PUT')
    @endif

    <div class="card">
        <h2 class="card__title admin-form__title">Brand info</h2>
        <div class="admin-form__fields">
            <div class="form-group">
                <label class="form-label" for="name">Name</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $brand->name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="slug">Slug</label>
                <input type="text" id="slug" name="slug" class="form-input" value="{{ old('slug', $brand->slug ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-textarea">{{ old('description', $brand->description ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="admin-form__actions">
        <button type="submit" class="btn btn--primary">{{ isset($brand) ? 'Save brand' : 'Create brand' }}</button>
        <a href="{{ route('admin.brands.index') }}" class="btn btn--ghost">Cancel</a>
    </div>
</form>
@endsection
