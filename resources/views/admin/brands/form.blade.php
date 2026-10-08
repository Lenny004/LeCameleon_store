@extends('layouts.admin')

@section('title', ($brand->name ?? 'Nueva marca') . ' — Marcas')
@section('page-title', isset($brand) ? 'Editar marca' : 'Nueva marca')

@section('content')
<form method="POST" action="{{ isset($brand) ? route('admin.brands.update', $brand) : route('admin.brands.store') }}" class="admin-form admin-form--narrow">
    @csrf
    @if (isset($brand))
        @method('PUT')
    @endif

    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>

    <div class="card">
        <h2 class="card__title admin-form__title">Datos de la marca</h2>
        <div class="admin-form__fields">
            <div class="form-group">
                <label class="form-label" for="name">Nombre <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="text" id="name" name="name" class="form-input @error('name') form-input--error @enderror" value="{{ old('name', $brand->name ?? '') }}" placeholder="Levi's" maxlength="150" required autocomplete="off" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                @error('name')<span class="form-error" id="name-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="slug">Slug <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="text" id="slug" name="slug" class="form-input @error('slug') form-input--error @enderror" value="{{ old('slug', $brand->slug ?? '') }}" placeholder="levis" maxlength="180" required autocomplete="off" @error('slug') aria-invalid="true" aria-describedby="slug-error" @enderror>
                @error('slug')<span class="form-error" id="slug-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Descripción</label>
                <textarea id="description" name="description" class="form-textarea @error('description') form-textarea--error @enderror" rows="5" maxlength="5000" placeholder="Describe la marca y su historia." @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>{{ old('description', $brand->description ?? '') }}</textarea>
                @error('description')<span class="form-error" id="description-error">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="admin-form__actions">
        <button type="submit" class="btn btn--primary">{{ isset($brand) ? 'Guardar marca' : 'Crear marca' }}</button>
        <a href="{{ route('admin.brands.index') }}" class="btn btn--ghost">Cancelar</a>
    </div>
</form>
@endsection
