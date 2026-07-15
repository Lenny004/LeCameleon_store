@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<form method="POST" action="{{ Route::has('admin.settings.update') ? route('admin.settings.update') : '#' }}" style="max-width:36rem;display:flex;flex-direction:column;gap:var(--space-xl);">
    @csrf
    @method('PUT')

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-lg);">Store</h2>
        <div style="display:flex;flex-direction:column;gap:var(--space-md);">
            <div class="form-group">
                <label class="form-label" for="store_name">Store name</label>
                <input type="text" id="store_name" name="store_name" class="form-input" value="{{ old('store_name', $settings['store_name'] ?? 'Le Cameleon') }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="store_email">Contact email</label>
                <input type="email" id="store_email" name="store_email" class="form-input" value="{{ old('store_email', $settings['store_email'] ?? 'hola@lecameleon.com') }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="currency">Currency</label>
                <select id="currency" name="currency" class="form-select">
                    <option value="USD" selected>USD</option>
                    <option value="MXN">MXN</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-lg);">Shipping</h2>
        <div class="form-group">
            <label class="form-label" for="flat_rate">Flat rate shipping</label>
            <input type="number" id="flat_rate" name="flat_rate" class="form-input" step="0.01" value="{{ old('flat_rate', $settings['flat_rate'] ?? 9.99) }}">
        </div>
        <div class="form-group">
            <label class="form-label" for="free_shipping_threshold">Free shipping over</label>
            <input type="number" id="free_shipping_threshold" name="free_shipping_threshold" class="form-input" step="0.01" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? 150) }}">
        </div>
    </div>

    <button type="submit" class="btn btn--primary" style="width:fit-content;">Save settings</button>
</form>
@endsection
