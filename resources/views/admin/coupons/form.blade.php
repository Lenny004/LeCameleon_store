@extends('layouts.admin')

@section('title', ($coupon->code ?? 'Nuevo cupón') . ' — Cupones')
@section('page-title', isset($coupon) ? 'Editar cupón' : 'Nuevo cupón')

@section('content')
<form method="POST" action="{{ isset($coupon) ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" class="admin-form admin-form--narrow">
    @csrf
    @if (isset($coupon))
        @method('PUT')
    @endif

    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>

    <div class="card">
        <h2 class="card__title admin-form__title">Datos del cupón</h2>
        <div class="admin-form__fields">
            <div class="form-group">
                <label class="form-label" for="code">Código <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="text" id="code" name="code" class="form-input form-input--uppercase @error('code') form-input--error @enderror" value="{{ old('code', $coupon->code ?? '') }}" placeholder="VERANO20" maxlength="50" required autocomplete="off" @error('code') aria-invalid="true" aria-describedby="code-error" @enderror>
                @error('code')<span class="form-error" id="code-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-row form-row--cols-2">
                <div class="form-group">
                    <label class="form-label" for="type">Tipo <span class="form-label__required" aria-hidden="true">*</span></label>
                    <select id="type" name="type" class="form-select @error('type') form-select--error @enderror" required @error('type') aria-invalid="true" aria-describedby="type-error" @enderror>
                        <option value="" disabled {{ old('type', $coupon->type->value ?? '') === '' ? 'selected' : '' }}>Selecciona un tipo</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" {{ old('type', $coupon->type->value ?? '') === $type->value ? 'selected' : '' }}>{{ $type->value === 'percent' ? 'Porcentaje' : 'Monto fijo' }}</option>
                        @endforeach
                    </select>
                    @error('type')<span class="form-error" id="type-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="value">Valor <span class="form-label__required" aria-hidden="true">*</span></label>
                    <input type="number" id="value" name="value" class="form-input @error('value') form-input--error @enderror" step="0.01" min="0" max="9999999999.99" inputmode="decimal" placeholder="25.00" value="{{ old('value', $coupon->value ?? '') }}" required @error('value') aria-invalid="true" aria-describedby="value-error" @enderror>
                    @error('value')<span class="form-error" id="value-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-row form-row--cols-2">
                <div class="form-group">
                    <label class="form-label" for="min_order_amount">Monto mínimo del pedido</label>
                    <input type="number" id="min_order_amount" name="min_order_amount" class="form-input @error('min_order_amount') form-input--error @enderror" step="0.01" min="0" max="9999999999.99" inputmode="decimal" placeholder="50.00" value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}" @error('min_order_amount') aria-invalid="true" aria-describedby="min_order_amount-error" @enderror>
                    @error('min_order_amount')<span class="form-error" id="min_order_amount-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="max_uses">Máximo de usos</label>
                    <input type="number" id="max_uses" name="max_uses" class="form-input @error('max_uses') form-input--error @enderror" min="1" max="2147483647" step="1" inputmode="numeric" placeholder="100" value="{{ old('max_uses', $coupon->max_uses ?? '') }}" @error('max_uses') aria-invalid="true" aria-describedby="max_uses-error" @enderror>
                    @error('max_uses')<span class="form-error" id="max_uses-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-row form-row--cols-2">
                <div class="form-group">
                    <label class="form-label" for="starts_at">Fecha de inicio</label>
                    <input type="datetime-local" id="starts_at" name="starts_at" class="form-input @error('starts_at') form-input--error @enderror" value="{{ old('starts_at', isset($coupon->starts_at) ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}" @error('starts_at') aria-invalid="true" aria-describedby="starts_at-error" @enderror>
                    @error('starts_at')<span class="form-error" id="starts_at-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="ends_at">Fecha de finalización</label>
                    <input type="datetime-local" id="ends_at" name="ends_at" class="form-input @error('ends_at') form-input--error @enderror" value="{{ old('ends_at', isset($coupon->ends_at) ? $coupon->ends_at->format('Y-m-d\TH:i') : '') }}" @error('ends_at') aria-invalid="true" aria-describedby="ends_at-error" @enderror>
                    @error('ends_at')<span class="form-error" id="ends_at-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
                    Activo
                </label>
            </div>
            <div class="form-group">
                <label class="form-label">
                    <input type="hidden" name="shipping_only" value="0">
                    <input type="checkbox" name="shipping_only" value="1" {{ old('shipping_only', $coupon->shipping_only ?? false) ? 'checked' : '' }}>
                    Aplicar el descuento solo al envío
                </label>
            </div>
            <div class="form-group">
                <label class="form-label" for="max_uses_per_user">Usos máximos por cliente</label>
        <input type="number" id="max_uses_per_user" name="max_uses_per_user" class="form-input @error('max_uses_per_user') form-input--error @enderror" min="1" max="4294967295" step="1" inputmode="numeric" placeholder="2" value="{{ old('max_uses_per_user', $coupon->max_uses_per_user ?? '') }}" @error('max_uses_per_user') aria-invalid="true" aria-describedby="max-uses-per-user-error" @enderror>
                @error('max_uses_per_user')<span class="form-error" id="max-uses-per-user-error">{{ $message }}</span>@enderror
            </div>
            <label class="form-checkbox"><input type="checkbox" name="first_order_only" value="1" @checked(old('first_order_only', $coupon->first_order_only ?? false))> Solo primera compra</label>
            <div class="form-group">
                <label class="form-label" for="category_ids">Categorías elegibles</label>
        <select id="category_ids" name="category_ids[]" class="form-select" multiple size="5">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(in_array($category->id, old('category_ids', isset($coupon) ? $coupon->categories->pluck('id')->all() : [])))>{{ $category->name }}</option>
            @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="product_ids">Productos elegibles</label>
        <select id="product_ids" name="product_ids[]" class="form-select" multiple size="6">
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected(in_array($product->id, old('product_ids', isset($coupon) ? $coupon->products->pluck('id')->all() : [])))>{{ $product->sku }} — {{ $product->name }}</option>
            @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="admin-form__actions">
        <button type="submit" class="btn btn--primary">{{ isset($coupon) ? 'Guardar cupón' : 'Crear cupón' }}</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn--ghost">Cancelar</a>
    </div>
</form>

<script nonce="{{ Vite::cspNonce() }}">
    (function () {
        var type = document.getElementById('type');
        var value = document.getElementById('value');

        function syncValueLimit() {
            if (type.value === 'percent') {
                value.max = '100';
            } else {
                value.max = '9999999999.99';
            }
        }

        type.addEventListener('change', syncValueLimit);
        syncValueLimit();
    })();
</script>
@endsection
