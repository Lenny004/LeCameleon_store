@extends('layouts.admin')

@section('title', $municipality->name . ' — Municipios')
@section('page-title', 'Editar municipio')

@section('content')
<form method="POST" action="{{ route('admin.logistics.municipalities.update', $municipality) }}" class="logistics-form">
    @csrf
    @method('PUT')
    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
    <div class="card">
        <h2 class="card__title logistics-form__heading">{{ $municipality->name }}</h2>
        <p class="text-muted admin-form__hint admin-form__hint--bottom">Departamento: {{ $municipality->department->name }}</p>
        <div class="logistics-form__grid">
            <div class="form-group">
                <label class="form-label" for="base_shipping_cost">Costo base de envío ($) <span class="form-label__required" aria-hidden="true">*</span></label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    max="99999999.99"
                    inputmode="decimal"
                    id="base_shipping_cost"
                    name="base_shipping_cost"
                    class="form-input @error('base_shipping_cost') form-input--error @enderror"
                    value="{{ old('base_shipping_cost', $municipality->base_shipping_cost) }}"
                    placeholder="5.00"
                    required
                    @error('base_shipping_cost') aria-invalid="true" aria-describedby="base_shipping_cost-error" @enderror
                >
                @error('base_shipping_cost')<span class="form-error" id="base_shipping_cost-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="latitude">Latitud</label>
                <input
                    type="number"
                    step="0.0000001"
                    min="-90"
                    max="90"
                    inputmode="decimal"
                    id="latitude"
                    name="latitude"
                    class="form-input @error('latitude') form-input--error @enderror"
                    value="{{ old('latitude', $municipality->latitude) }}"
                    placeholder="13.6929"
                    @error('latitude') aria-invalid="true" aria-describedby="latitude-error" @enderror
                >
                @error('latitude')<span class="form-error" id="latitude-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="longitude">Longitud</label>
                <input
                    type="number"
                    step="0.0000001"
                    min="-180"
                    max="180"
                    inputmode="decimal"
                    id="longitude"
                    name="longitude"
                    class="form-input @error('longitude') form-input--error @enderror"
                    value="{{ old('longitude', $municipality->longitude) }}"
                    placeholder="-89.2182"
                    @error('longitude') aria-invalid="true" aria-describedby="longitude-error" @enderror
                >
                @error('longitude')<span class="form-error" id="longitude-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label logistics-checkbox">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $municipality->is_active))>
                    Municipio habilitado
                </label>
            </div>
        </div>
    </div>
    <div class="logistics-form__actions">
        <button type="submit" class="btn btn--primary">Guardar municipio</button>
        <a href="{{ route('admin.logistics.municipalities.index') }}" class="btn btn--ghost">Cancelar</a>
    </div>
</form>
@endsection
