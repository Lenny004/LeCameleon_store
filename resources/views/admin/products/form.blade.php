@extends('layouts.admin')

@section('title', ($product?->name ?? 'Nuevo producto') . ' — Productos')
@section('page-title', isset($product) ? 'Editar producto' : 'Nuevo producto')

@section('content')
{{-- Field names must match Admin\ProductRequest and the Product model. --}}
@php
    $isEditing = isset($product);
    $formAction = $isEditing
        ? route('admin.products.update', $product)
        : route('admin.products.store');
@endphp

<form
    method="POST"
    action="{{ $formAction }}"
    enctype="multipart/form-data"
    class="admin-form admin-form--wide"
>
    @csrf
    @if ($isEditing)
        @method('PUT')
    @endif

    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>

    @if ($errors->any())
        <div class="flash flash--error" role="alert">
            <ul class="admin-form__list">
                @foreach ($errors->all() as $errorMessage)
                    <li>{{ $errorMessage }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <h2 class="card__title admin-form__title">Información básica</h2>
        <div class="admin-form__fields">
            <div class="form-group">
                <label class="form-label" for="name">Nombre <span class="form-label__required" aria-hidden="true">*</span></label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-input @error('name') form-input--error @enderror"
                    value="{{ old('name', $product?->name ?? '') }}"
                    placeholder="Vestido floral de los años 70"
                    maxlength="255"
                    required
                    autocomplete="off"
                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                >
                @error('name')<span class="form-error" id="name-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-row form-row--cols-2">
                <div class="form-group">
                    <label class="form-label" for="slug">Slug <span class="form-label__required" aria-hidden="true">*</span></label>
                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        class="form-input @error('slug') form-input--error @enderror"
                        value="{{ old('slug', $product->slug ?? '') }}"
                        placeholder="vestido-floral-anos-70"
                        maxlength="280"
                        required
                        autocomplete="off"
                        @error('slug') aria-invalid="true" aria-describedby="slug-error" @enderror
                    >
                    @error('slug')<span class="form-error" id="slug-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="sku">SKU <span class="form-label__required" aria-hidden="true">*</span></label>
                    <input
                        type="text"
                        id="sku"
                        name="sku"
                        class="form-input @error('sku') form-input--error @enderror"
                        value="{{ old('sku', $product->sku ?? '') }}"
                        placeholder="VST-1970-001"
                        maxlength="80"
                        required
                        autocomplete="off"
                        @error('sku') aria-invalid="true" aria-describedby="sku-error" @enderror
                    >
                    @error('sku')<span class="form-error" id="sku-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row form-row--cols-2">
                <div class="form-group">
                    <label class="form-label" for="type">Tipo <span class="form-label__required" aria-hidden="true">*</span></label>
                    <select id="type" name="type" class="form-select @error('type') form-select--error @enderror" required @error('type') aria-invalid="true" aria-describedby="type-error" @enderror>
                        <option value="" disabled {{ old('type', $product?->type?->value ?? 'apparel') === '' ? 'selected' : '' }}>Selecciona un tipo</option>
                        @foreach (['apparel' => 'Apparel', 'object' => 'Object', 'accessory' => 'Accessory'] as $typeValue => $typeLabel)
                            <option value="{{ $typeValue }}" @selected(old('type', $product?->type?->value ?? 'apparel') === $typeValue)>
                                {{ ['apparel' => 'Ropa', 'object' => 'Objeto', 'accessory' => 'Accesorio'][$typeValue] }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')<span class="form-error" id="type-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="status">Estado <span class="form-label__required" aria-hidden="true">*</span></label>
                    <select id="status" name="status" class="form-select @error('status') form-select--error @enderror" required @error('status') aria-invalid="true" aria-describedby="status-error" @enderror>
                        <option value="" disabled {{ old('status', $product?->status?->value ?? 'draft') === '' ? 'selected' : '' }}>Selecciona un estado</option>
                        @foreach (['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived', 'sold_out' => 'Sold out'] as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}" @selected(old('status', $product?->status?->value ?? 'draft') === $statusValue)>
                                {{ ['draft' => 'Borrador', 'published' => 'Publicado', 'archived' => 'Archivado', 'sold_out' => 'Agotado'][$statusValue] }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')<span class="form-error" id="status-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row form-row--cols-2">
                <div class="form-group">
                    <label class="form-label" for="brand_id">Marca</label>
                    <select id="brand_id" name="brand_id" class="form-select @error('brand_id') form-select--error @enderror" @error('brand_id') aria-invalid="true" aria-describedby="brand_id-error" @enderror>
                        <option value="">— Ninguna —</option>
                        @foreach ($brands ?? [] as $brand)
                            <option value="{{ $brand->id }}" @selected((string) old('brand_id', $product?->brand_id ?? '') === (string) $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('brand_id')<span class="form-error" id="brand_id-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="category_id">Categoría</label>
                    <select
                        id="category_id"
                        name="category_id"
                        class="form-select @error('category_id') form-select--error @enderror"
                        @error('category_id') aria-invalid="true" aria-describedby="category_id-error" @enderror
                    >
                        <option value="">— Ninguna —</option>
                        @foreach ($categories ?? [] as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $product?->category_id ?? '') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="form-error" id="category_id-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="short_description">Descripción corta</label>
                <input
                    type="text"
                    id="short_description"
                    name="short_description"
                    class="form-input @error('short_description') form-input--error @enderror"
                    maxlength="500"
                    value="{{ old('short_description', $product->short_description ?? '') }}"
                    placeholder="Vestido estampado en excelente estado"
                    @error('short_description') aria-invalid="true" aria-describedby="short_description-error" @enderror
                >
                @error('short_description')<span class="form-error" id="short_description-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Descripción</label>
                <textarea
                    id="description"
                    name="description"
                    class="form-textarea @error('description') form-textarea--error @enderror"
                    rows="5"
                    maxlength="5000"
                    placeholder="Describe la pieza, sus detalles y su historia."
                    @error('description') aria-invalid="true" aria-describedby="description-error" @enderror
                >{{ old('description', $product->description ?? '') }}</textarea>
                @error('description')<span class="form-error" id="description-error">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card__title admin-form__title">Precios y existencias</h2>
        <div class="form-row form-row--cols-2">
            <div class="form-group">
                <label class="form-label" for="price">Precio <span class="form-label__required" aria-hidden="true">*</span></label>
                <input
                    type="number"
                    id="price"
                    name="price"
                    class="form-input @error('price') form-input--error @enderror"
                    step="0.01"
                    min="0"
                    max="9999999999.99"
                    inputmode="decimal"
                    placeholder="25.00"
                    value="{{ old('price', $product->price ?? '') }}"
                    required
                    @error('price') aria-invalid="true" aria-describedby="price-error" @enderror
                >
                @error('price')<span class="form-error" id="price-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="compare_at_price">Precio anterior</label>
                <input
                    type="number"
                    id="compare_at_price"
                    name="compare_at_price"
                    class="form-input @error('compare_at_price') form-input--error @enderror"
                    step="0.01"
                    min="0"
                    max="9999999999.99"
                    inputmode="decimal"
                    placeholder="35.00"
                    value="{{ old('compare_at_price', $product->compare_at_price ?? '') }}"
                    @error('compare_at_price') aria-invalid="true" aria-describedby="compare_at_price-error" @enderror
                >
                @error('compare_at_price')<span class="form-error" id="compare_at_price-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="form-row form-row--cols-2">
            <div class="form-group">
                <label class="form-label" for="cost_price">Precio de costo</label>
                <input
                    type="number"
                    id="cost_price"
                    name="cost_price"
                    class="form-input @error('cost_price') form-input--error @enderror"
                    step="0.01"
                    min="0"
                    max="9999999999.99"
                    inputmode="decimal"
                    placeholder="12.50"
                    value="{{ old('cost_price', $product->cost_price ?? '') }}"
                    @error('cost_price') aria-invalid="true" aria-describedby="cost_price-error" @enderror
                >
                @error('cost_price')<span class="form-error" id="cost_price-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="quantity_available">Cantidad disponible <span class="form-label__required" aria-hidden="true">*</span></label>
                <input
                    type="number"
                    id="quantity_available"
                    name="quantity_available"
                    class="form-input @error('quantity_available') form-input--error @enderror"
                    min="0"
                    max="2147483647"
                    step="1"
                    inputmode="numeric"
                    placeholder="1"
                    value="{{ old('quantity_available', $product->quantity_available ?? 1) }}"
                    required
                    @error('quantity_available') aria-invalid="true" aria-describedby="quantity_available-error" @enderror
                >
                @error('quantity_available')<span class="form-error" id="quantity_available-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="form-row form-row--cols-2">
            <div class="form-group">
                <label class="form-label" for="low_stock_threshold">Umbral de inventario bajo</label>
                <input
                    type="number"
                    id="low_stock_threshold"
                    name="low_stock_threshold"
                    class="form-input @error('low_stock_threshold') form-input--error @enderror"
                    min="0"
                    max="32767"
                    step="1"
                    inputmode="numeric"
                    placeholder="1"
                    value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 1) }}"
                    @error('low_stock_threshold') aria-invalid="true" aria-describedby="low_stock_threshold-error" @enderror
                >
                @error('low_stock_threshold')<span class="form-error" id="low_stock_threshold-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="is_unique_piece">
                    <input type="checkbox" id="is_unique_piece" name="is_unique_piece" value="1" @checked(old('is_unique_piece', $product->is_unique_piece ?? true))>
                    Pieza única (1 de 1)
                </label>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card__title admin-form__title">Atributos vintage</h2>
        <div class="form-row form-row--cols-2">
            <div class="form-group">
                <label class="form-label" for="condition_grade">Condición <span class="form-label__required" aria-hidden="true">*</span></label>
                <select
                    id="condition_grade"
                    name="condition_grade"
                    class="form-select @error('condition_grade') form-select--error @enderror"
                    required
                    @error('condition_grade') aria-invalid="true" aria-describedby="condition_grade-error" @enderror
                >
                    <option value="" disabled {{ old('condition_grade', $product?->condition_grade?->value ?? 'good') === '' ? 'selected' : '' }}>Selecciona una condición</option>
                    @foreach (['mint' => 'Mint', 'excellent' => 'Excellent', 'good' => 'Good', 'fair' => 'Fair', 'poor' => 'Poor'] as $gradeValue => $gradeLabel)
                        <option value="{{ $gradeValue }}" @selected(old('condition_grade', $product?->condition_grade?->value ?? 'good') === $gradeValue)>
                            {{ ['mint' => 'Impecable', 'excellent' => 'Excelente', 'good' => 'Buena', 'fair' => 'Regular', 'poor' => 'Deficiente'][$gradeValue] }}
                        </option>
                    @endforeach
                </select>
                @error('condition_grade')<span class="form-error" id="condition_grade-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="era_decade">Década</label>
                <select id="era_decade" name="era_decade" class="form-select @error('era_decade') form-select--error @enderror" @error('era_decade') aria-invalid="true" aria-describedby="era_decade-error" @enderror>
                    <option value="">—</option>
                    @foreach (['1950s', '1960s', '1970s', '1980s', '1990s', '2000s'] as $eraDecade)
                        <option value="{{ $eraDecade }}" @selected(old('era_decade', $product?->era_decade ?? '') === $eraDecade)>{{ $eraDecade }}</option>
                    @endforeach
                </select>
                @error('era_decade')<span class="form-error" id="era_decade-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="form-row form-row--cols-2">
            <div class="form-group">
                    <label class="form-label" for="size_label">Talla</label>
                    <input
                        type="text"
                        id="size_label"
                        name="size_label"
                        class="form-input @error('size_label') form-input--error @enderror"
                        value="{{ old('size_label', $product->size_label ?? '') }}"
                        placeholder="M / EU 38"
                        maxlength="50"
                        @error('size_label') aria-invalid="true" aria-describedby="size_label-error" @enderror
                    >
                    @error('size_label')<span class="form-error" id="size_label-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="color">Color</label>
                <input
                    type="text"
                    id="color"
                    name="color"
                    class="form-input @error('color') form-input--error @enderror"
                    value="{{ old('color', $product->color ?? '') }}"
                    placeholder="Azul marino"
                    maxlength="80"
                    @error('color') aria-invalid="true" aria-describedby="color-error" @enderror
                >
                @error('color')<span class="form-error" id="color-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="material">Material</label>
            <input
                type="text"
                id="material"
                name="material"
                class="form-input @error('material') form-input--error @enderror"
                value="{{ old('material', $product->material ?? '') }}"
                placeholder="Algodón y lino"
                maxlength="150"
                @error('material') aria-invalid="true" aria-describedby="material-error" @enderror
            >
            @error('material')<span class="form-error" id="material-error">{{ $message }}</span>@enderror
        </div>

        <h3 class="text-small admin-form__heading">Medidas (cm)</h3>
        <p class="text-muted admin-form__hint admin-form__hint--bottom">Medidas opcionales de la prenda extendida para la página de detalle.</p>
        @php
            $measurementLabels = [
                'chest_cm' => 'Pecho',
                'waist_cm' => 'Cintura',
                'hips_cm' => 'Cadera',
                'length_cm' => 'Largo',
                'shoulder_cm' => 'Hombros',
                'sleeve_cm' => 'Manga',
            ];
            $existingMeasurements = old('measurements', $product?->measurements ?? []);
        @endphp
        <div class="form-row form-row--cols-2">
            @foreach ($measurementLabels as $measurementKey => $measurementLabel)
                <div class="form-group">
                    <label class="form-label" for="measurement_{{ $measurementKey }}">{{ $measurementLabel }} (cm)</label>
                    <input
                        type="number"
                        id="measurement_{{ $measurementKey }}"
                        name="measurement_{{ $measurementKey }}"
                        class="form-input @error("measurements.{$measurementKey}") form-input--error @enderror"
                        step="0.1"
                        min="0"
                        max="9999.9"
                        inputmode="decimal"
                        placeholder="50.0"
                        value="{{ old("measurement_{$measurementKey}", $existingMeasurements[$measurementKey] ?? '') }}"
                        @error("measurements.{$measurementKey}") aria-invalid="true" aria-describedby="measurement_{{ $measurementKey }}-error" @enderror
                    >
                    @error("measurements.{$measurementKey}")<span class="form-error" id="measurement_{{ $measurementKey }}-error">{{ $message }}</span>@enderror
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <h2 class="card__title admin-form__title">Autenticidad</h2>
        <div class="admin-form__fields">
            <div class="form-group">
                <label class="form-label" for="is_authenticated">
                    <input type="checkbox" id="is_authenticated" name="is_authenticated" value="1" @checked(old('is_authenticated', $product->is_authenticated ?? false))>
                    Pieza verificada (procedencia comprobada)
                </label>
            </div>
            <div class="form-group">
                <label class="form-label" for="authenticity_notes">Notas de autenticidad</label>
                <textarea
                    id="authenticity_notes"
                    name="authenticity_notes"
                    class="form-textarea @error('authenticity_notes') form-textarea--error @enderror"
                    rows="3"
                    maxlength="2000"
                    placeholder="Número de serie, revisión experta o resumen de documentos…"
                    @error('authenticity_notes') aria-invalid="true" aria-describedby="authenticity_notes-error" @enderror
                >{{ old('authenticity_notes', $product->authenticity_notes ?? '') }}</textarea>
                @error('authenticity_notes')<span class="form-error" id="authenticity_notes-error">{{ $message }}</span>@enderror
                <p class="text-muted admin-form__hint">Se muestra en la página del producto cuando está verificado.</p>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card__title admin-form__title">Imágenes</h2>

        @if ($isEditing && $product->images->isNotEmpty())
            <input type="hidden" name="manage_images" value="1">
            <p class="text-muted admin-form__hint admin-form__hint--bottom">Desmarca las imágenes que quieras eliminar al guardar.</p>
            <div class="admin-form__image-grid">
                @foreach ($product->images as $image)
                    <label class="admin-form__image-field">
                        <img class="admin-form__image" src="{{ asset('storage/'.$image->path) }}" alt="{{ $image->alt }}">
                        <span>
                            <input
                                type="checkbox"
                                name="keep_image_ids[]"
                                value="{{ $image->id }}"
                                @checked(in_array($image->id, old('keep_image_ids', $product->images->pluck('id')->all())))
                            >
                            Conservar@if ($image->is_primary) (principal)@endif
                        </span>
                    </label>
                @endforeach
            </div>
        @endif

        <div class="form-group">
            <label class="form-label" for="images">Subir imágenes</label>
            <input
                type="file"
                id="images"
                name="images[]"
                class="form-input @error('images.*') form-input--error @enderror"
                accept="image/*"
                multiple
                aria-describedby="images-hint @error('images.*')images-error @enderror"
                @error('images.*') aria-invalid="true" @enderror
            >
            <p id="images-hint" class="text-muted admin-form__hint">La primera imagen subida será principal si no existe una.</p>
            @error('images.*')<span class="form-error" id="images-error">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="card">
        <h2 class="card__title admin-form__title">SEO</h2>
        <div class="form-group">
            <label class="form-label" for="meta_title">Título SEO</label>
            <input
                type="text"
                id="meta_title"
                name="meta_title"
                class="form-input @error('meta_title') form-input--error @enderror"
                maxlength="255"
                value="{{ old('meta_title', $product->meta_title ?? '') }}"
                placeholder="Vestido floral vintage"
                @error('meta_title') aria-invalid="true" aria-describedby="meta_title-error" @enderror
            >
            @error('meta_title')<span class="form-error" id="meta_title-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="meta_description">Descripción SEO</label>
            <textarea
                id="meta_description"
                name="meta_description"
                class="form-textarea @error('meta_description') form-textarea--error @enderror"
                rows="2"
                maxlength="500"
                placeholder="Resumen breve para buscadores."
                @error('meta_description') aria-invalid="true" aria-describedby="meta_description-error" @enderror
            >{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
            @error('meta_description')<span class="form-error" id="meta_description-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="published_at">Fecha de publicación</label>
            <input
                type="datetime-local"
                id="published_at"
                name="published_at"
                class="form-input @error('published_at') form-input--error @enderror"
                value="{{ old('published_at', isset($product) && $product->published_at ? $product->published_at->format('Y-m-d\TH:i') : '') }}"
                @error('published_at') aria-invalid="true" aria-describedby="published_at-error" @enderror
            >
            @error('published_at')<span class="form-error" id="published_at-error">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="admin-form__actions">
        <button type="submit" class="btn btn--primary">Guardar producto</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn--ghost">Cancelar</a>
    </div>
</form>
@endsection
