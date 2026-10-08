@extends('layouts.admin')

@section('title', ($coupon->code ?? 'New coupon') . ' — Coupons')
@section('page-title', isset($coupon) ? 'Edit coupon' : 'New coupon')

@section('content')
<form method="POST" action="{{ isset($coupon) ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" class="admin-form admin-form--narrow">
    @csrf
    @if (isset($coupon))
        @method('PUT')
    @endif

    <div class="card">
        <h2 class="card__title admin-form__title">Coupon details</h2>
        <div class="admin-form__fields">
            <div class="form-group">
                <label class="form-label" for="code">Code</label>
                <input type="text" id="code" name="code" class="form-input form-input--uppercase" value="{{ old('code', $coupon->code ?? '') }}" required>
            </div>
            <div class="form-row form-row--cols-2">
                <div class="form-group">
                    <label class="form-label" for="type">Type</label>
                    <select id="type" name="type" class="form-select" required>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" {{ old('type', $coupon->type->value ?? '') === $type->value ? 'selected' : '' }}>{{ ucfirst($type->value) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="value">Value</label>
                    <input type="number" id="value" name="value" class="form-input" step="0.01" min="0" value="{{ old('value', $coupon->value ?? '') }}" required>
                </div>
            </div>
            <div class="form-row form-row--cols-2">
                <div class="form-group">
                    <label class="form-label" for="min_order_amount">Min order amount</label>
                    <input type="number" id="min_order_amount" name="min_order_amount" class="form-input" step="0.01" min="0" value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="max_uses">Max uses</label>
                    <input type="number" id="max_uses" name="max_uses" class="form-input" min="1" value="{{ old('max_uses', $coupon->max_uses ?? '') }}">
                </div>
            </div>
            <div class="form-row form-row--cols-2">
                <div class="form-group">
                    <label class="form-label" for="starts_at">Starts at</label>
                    <input type="datetime-local" id="starts_at" name="starts_at" class="form-input" value="{{ old('starts_at', isset($coupon->starts_at) ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="ends_at">Ends at</label>
                    <input type="datetime-local" id="ends_at" name="ends_at" class="form-input" value="{{ old('ends_at', isset($coupon->ends_at) ? $coupon->ends_at->format('Y-m-d\TH:i') : '') }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
                    Active
                </label>
            </div>
            <div class="form-group">
                <label class="form-label">
                    <input type="hidden" name="shipping_only" value="0">
                    <input type="checkbox" name="shipping_only" value="1" {{ old('shipping_only', $coupon->shipping_only ?? false) ? 'checked' : '' }}>
                    Apply discount to shipping only
                </label>
            </div>
        </div>
    </div>

    <div class="admin-form__actions">
        <button type="submit" class="btn btn--primary">{{ isset($coupon) ? 'Save coupon' : 'Create coupon' }}</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn--ghost">Cancel</a>
    </div>
</form>

<script>
    (function () {
        var type = document.getElementById('type');
        var value = document.getElementById('value');

        function syncValueLimit() {
            if (type.value === 'percent') {
                value.max = '100';
            } else {
                value.removeAttribute('max');
            }
        }

        type.addEventListener('change', syncValueLimit);
        syncValueLimit();
    })();
</script>
@endsection
