@extends('layouts.admin')

@section('title', ($coupon->code ?? 'New coupon') . ' — Coupons')
@section('page-title', isset($coupon) ? 'Edit coupon' : 'New coupon')

@section('content')
<form method="POST" action="{{ isset($coupon) ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" style="max-width:40rem;display:flex;flex-direction:column;gap:var(--space-xl);">
    @csrf
    @if (isset($coupon))
        @method('PUT')
    @endif

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-lg);">Coupon details</h2>
        <div style="display:flex;flex-direction:column;gap:var(--space-md);">
            <div class="form-group">
                <label class="form-label" for="code">Code</label>
                <input type="text" id="code" name="code" class="form-input" value="{{ old('code', $coupon->code ?? '') }}" required style="text-transform:uppercase;">
            </div>
            <div class="form-row form-row--2">
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
            <div class="form-row form-row--2">
                <div class="form-group">
                    <label class="form-label" for="min_order_amount">Min order amount</label>
                    <input type="number" id="min_order_amount" name="min_order_amount" class="form-input" step="0.01" min="0" value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="max_uses">Max uses</label>
                    <input type="number" id="max_uses" name="max_uses" class="form-input" min="1" value="{{ old('max_uses', $coupon->max_uses ?? '') }}">
                </div>
            </div>
            <div class="form-row form-row--2">
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
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
                    Active
                </label>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:var(--space-sm);">
        <button type="submit" class="btn btn--primary">{{ isset($coupon) ? 'Save coupon' : 'Create coupon' }}</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn--ghost">Cancel</a>
    </div>
</form>
@endsection
