@extends('layouts.admin')

@section('title', $municipality->name . ' — Municipalities')
@section('page-title', 'Edit municipality')

@section('content')
<form method="POST" action="{{ route('admin.logistics.municipalities.update', $municipality) }}" class="logistics-form">
    @csrf
    @method('PUT')

    <div class="card">
        <h2 class="card__title logistics-form__heading">{{ $municipality->name }}</h2>
        <p class="text-muted" style="margin-bottom:var(--space-lg);">Department: {{ $municipality->department->name }}</p>
        <div class="logistics-form__grid">
            <div class="form-group">
                <label class="form-label" for="base_shipping_cost">Base shipping cost ($)</label>
                <input type="number" step="0.01" min="0" id="base_shipping_cost" name="base_shipping_cost" class="form-input" value="{{ old('base_shipping_cost', $municipality->base_shipping_cost) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="latitude">Latitude</label>
                <input type="number" step="0.0000001" id="latitude" name="latitude" class="form-input" value="{{ old('latitude', $municipality->latitude) }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="longitude">Longitude</label>
                <input type="number" step="0.0000001" id="longitude" name="longitude" class="form-input" value="{{ old('longitude', $municipality->longitude) }}">
            </div>
            <div class="form-group">
                <label class="form-label logistics-checkbox">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $municipality->is_active))>
                    Serviceable
                </label>
            </div>
        </div>
    </div>

    <div class="logistics-form__actions">
        <button type="submit" class="btn btn--primary">Save municipality</button>
        <a href="{{ route('admin.logistics.municipalities.index') }}" class="btn btn--ghost">Cancel</a>
    </div>
</form>
@endsection
