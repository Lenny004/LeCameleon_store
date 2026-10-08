@extends('layouts.admin')

@section('title', ($category->name ?? 'Nueva categoría') . ' — Categorías')
@section('page-title', isset($category) ? 'Editar categoría' : 'Nueva categoría')

@section('content')
<form method="POST" action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="admin-form admin-form--narrow">
    @csrf
    @if (isset($category))
        @method('PUT')
    @endif

    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>

    <div class="card">
        <h2 class="card__title admin-form__title">Datos de la categoría</h2>
        <div class="admin-form__fields">
            <div class="form-group">
                <label class="form-label" for="parent_id">Categoría superior</label>
                <select id="parent_id" name="parent_id" class="form-select @error('parent_id') form-select--error @enderror" @error('parent_id') aria-invalid="true" aria-describedby="parent_id-error" @enderror>
                    <option value="">— Ninguna —</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" {{ (string) old('parent_id', $category->parent_id ?? '') === (string) $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                    @endforeach
                </select>
                @error('parent_id')<span class="form-error" id="parent_id-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="name">Nombre <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="text" id="name" name="name" class="form-input @error('name') form-input--error @enderror" value="{{ old('name', $category->name ?? '') }}" placeholder="Vestidos vintage" maxlength="150" required autocomplete="off" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                @error('name')<span class="form-error" id="name-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="slug">Slug <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="text" id="slug" name="slug" class="form-input @error('slug') form-input--error @enderror" value="{{ old('slug', $category->slug ?? '') }}" placeholder="vestidos-vintage" maxlength="180" required autocomplete="off" @error('slug') aria-invalid="true" aria-describedby="slug-error" @enderror>
                @error('slug')<span class="form-error" id="slug-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Descripción</label>
                <textarea id="description" name="description" class="form-textarea @error('description') form-textarea--error @enderror" rows="5" maxlength="5000" placeholder="Describe la categoría y su estilo." @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>{{ old('description', $category->description ?? '') }}</textarea>
                @error('description')<span class="form-error" id="description-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-row form-row--cols-2">
                <div class="form-group">
                    <label class="form-label" for="sort_order">Orden</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-input @error('sort_order') form-input--error @enderror" min="0" max="32767" step="1" inputmode="numeric" placeholder="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}" @error('sort_order') aria-invalid="true" aria-describedby="sort_order-error" @enderror>
                    @error('sort_order')<span class="form-error" id="sort_order-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                        Activa
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-form__actions">
        <button type="submit" class="btn btn--primary">{{ isset($category) ? 'Guardar categoría' : 'Crear categoría' }}</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn--ghost">Cancelar</a>
    </div>
</form>
@endsection
