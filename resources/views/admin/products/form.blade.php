@extends('layouts.admin')

@section('title', ($product->name ?? 'New product') . ' — Products')
@section('page-title', isset($product) ? 'Edit product' : 'New product')

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
    style="max-width:48rem;display:flex;flex-direction:column;gap:var(--space-xl);"
>
    @csrf
    @if ($isEditing)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="flash flash--error" role="alert">
            <ul style="margin:0;padding-left:1.25rem;">
                @foreach ($errors->all() as $errorMessage)
                    <li>{{ $errorMessage }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-lg);">Basic info</h2>
        <div style="display:flex;flex-direction:column;gap:var(--space-md);">
            <div class="form-group">
                <label class="form-label" for="name">Name</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $product->name ?? '') }}" required>
            </div>

            <div class="form-row form-row--2">
                <div class="form-group">
                    <label class="form-label" for="slug">Slug</label>
                    <input type="text" id="slug" name="slug" class="form-input" value="{{ old('slug', $product->slug ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="sku">SKU</label>
                    <input type="text" id="sku" name="sku" class="form-input" value="{{ old('sku', $product->sku ?? '') }}" required>
                </div>
            </div>

            <div class="form-row form-row--2">
                <div class="form-group">
                    <label class="form-label" for="type">Type</label>
                    <select id="type" name="type" class="form-select" required>
                        @foreach (['apparel' => 'Apparel', 'object' => 'Object', 'accessory' => 'Accessory'] as $typeValue => $typeLabel)
                            <option value="{{ $typeValue }}" @selected(old('type', $product->type?->value ?? 'apparel') === $typeValue)>{{ $typeLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select" required>
                        @foreach (['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived', 'sold_out' => 'Sold out'] as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}" @selected(old('status', $product->status?->value ?? 'draft') === $statusValue)>{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row form-row--2">
                <div class="form-group">
                    <label class="form-label" for="brand_id">Brand</label>
                    <select id="brand_id" name="brand_id" class="form-select">
                        <option value="">— None —</option>
                        @foreach ($brands ?? [] as $brand)
                            <option value="{{ $brand->id }}" @selected((string) old('brand_id', $product->brand_id ?? '') === (string) $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="form-select">
                        <option value="">— None —</option>
                        @foreach ($categories ?? [] as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="short_description">Short description</label>
                <input type="text" id="short_description" name="short_description" class="form-input" maxlength="500" value="{{ old('short_description', $product->short_description ?? '') }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-textarea" rows="5">{{ old('description', $product->description ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-lg);">Pricing and stock</h2>
        <div class="form-row form-row--2">
            <div class="form-group">
                <label class="form-label" for="price">Price</label>
                <input type="number" id="price" name="price" class="form-input" step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="compare_at_price">Compare at price</label>
                <input type="number" id="compare_at_price" name="compare_at_price" class="form-input" step="0.01" min="0" value="{{ old('compare_at_price', $product->compare_at_price ?? '') }}">
            </div>
        </div>
        <div class="form-row form-row--2">
            <div class="form-group">
                <label class="form-label" for="cost_price">Cost price</label>
                <input type="number" id="cost_price" name="cost_price" class="form-input" step="0.01" min="0" value="{{ old('cost_price', $product->cost_price ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="quantity_available">Quantity available</label>
                <input type="number" id="quantity_available" name="quantity_available" class="form-input" min="0" value="{{ old('quantity_available', $product->quantity_available ?? 1) }}" required>
            </div>
        </div>
        <div class="form-row form-row--2">
            <div class="form-group">
                <label class="form-label" for="low_stock_threshold">Low stock threshold</label>
                <input type="number" id="low_stock_threshold" name="low_stock_threshold" class="form-input" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 1) }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="is_unique_piece">
                    <input type="checkbox" id="is_unique_piece" name="is_unique_piece" value="1" @checked(old('is_unique_piece', $product->is_unique_piece ?? true))>
                    Unique piece (1 of 1)
                </label>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-lg);">Vintage attributes</h2>
        <div class="form-row form-row--2">
            <div class="form-group">
                <label class="form-label" for="condition_grade">Condition</label>
                <select id="condition_grade" name="condition_grade" class="form-select" required>
                    @foreach (['mint' => 'Mint', 'excellent' => 'Excellent', 'good' => 'Good', 'fair' => 'Fair', 'poor' => 'Poor'] as $gradeValue => $gradeLabel)
                        <option value="{{ $gradeValue }}" @selected(old('condition_grade', $product->condition_grade?->value ?? 'good') === $gradeValue)>{{ $gradeLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="era_decade">Era decade</label>
                <select id="era_decade" name="era_decade" class="form-select">
                    <option value="">—</option>
                    @foreach (['1950s', '1960s', '1970s', '1980s', '1990s', '2000s'] as $eraDecade)
                        <option value="{{ $eraDecade }}" @selected(old('era_decade', $product->era_decade ?? '') === $eraDecade)>{{ $eraDecade }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row form-row--2">
            <div class="form-group">
                <label class="form-label" for="size_label">Size label</label>
                <input type="text" id="size_label" name="size_label" class="form-input" value="{{ old('size_label', $product->size_label ?? '') }}" placeholder="M / EU 38">
            </div>
            <div class="form-group">
                <label class="form-label" for="color">Color</label>
                <input type="text" id="color" name="color" class="form-input" value="{{ old('color', $product->color ?? '') }}">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="material">Material</label>
            <input type="text" id="material" name="material" class="form-input" value="{{ old('material', $product->material ?? '') }}">
        </div>
    </div>

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-lg);">Images</h2>

        @if ($isEditing && $product->images->isNotEmpty())
            <input type="hidden" name="manage_images" value="1">
            <p class="text-muted" style="margin-bottom:var(--space-md);">Uncheck images to remove them on save.</p>
            <div style="display:flex;flex-wrap:wrap;gap:var(--space-md);margin-bottom:var(--space-lg);">
                @foreach ($product->images as $image)
                    <label style="display:flex;flex-direction:column;gap:var(--space-xs);max-width:8rem;font-size:0.85rem;">
                        <img src="{{ asset('storage/'.$image->path) }}" alt="{{ $image->alt }}" style="width:8rem;height:8rem;object-fit:cover;border-radius:4px;">
                        <span>
                            <input
                                type="checkbox"
                                name="keep_image_ids[]"
                                value="{{ $image->id }}"
                                @checked(in_array($image->id, old('keep_image_ids', $product->images->pluck('id')->all())))
                            >
                            Keep@if ($image->is_primary) (primary)@endif
                        </span>
                    </label>
                @endforeach
            </div>
        @endif

        <div class="form-group">
            <label class="form-label" for="images">Upload images</label>
            <input type="file" id="images" name="images[]" class="form-input" accept="image/*" multiple>
            <p class="text-muted" style="margin-top:var(--space-xs);font-size:0.85rem;">First uploaded image becomes primary when none exists.</p>
        </div>
    </div>

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-lg);">SEO</h2>
        <div class="form-group">
            <label class="form-label" for="meta_title">Meta title</label>
            <input type="text" id="meta_title" name="meta_title" class="form-input" value="{{ old('meta_title', $product->meta_title ?? '') }}">
        </div>
        <div class="form-group">
            <label class="form-label" for="meta_description">Meta description</label>
            <textarea id="meta_description" name="meta_description" class="form-textarea" rows="2">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label" for="published_at">Published at</label>
            <input type="datetime-local" id="published_at" name="published_at" class="form-input" value="{{ old('published_at', isset($product) && $product->published_at ? $product->published_at->format('Y-m-d\TH:i') : '') }}">
        </div>
    </div>

    <div style="display:flex;gap:var(--space-sm);">
        <button type="submit" class="btn btn--primary">Save product</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn--ghost">Cancel</a>
    </div>
</form>
@endsection
