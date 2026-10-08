@extends('layouts.admin')

@section('title', 'Zonas de envío')
@section('page-title', 'Zonas de envío')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Zonas de envío</h2>
        <p class="admin-page-header__subtitle">Puntos de referencia para la matriz de tarifas.</p>
    </div>
    <a href="{{ route('admin.logistics.zones.rates-matrix') }}" class="btn btn--primary">Matriz de tarifas</a>
</div>
<div class="card admin-panel admin-panel--spaced">
    <h2 class="card__title logistics-form__heading">Agregar zona</h2>
    <form method="POST" action="{{ route('admin.logistics.zones.store') }}" class="logistics-form__grid">
        @csrf
        <p class="form-required-note logistics-form__full">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
        <div class="form-group">
            <label class="form-label" for="code">Código <span class="form-label__required" aria-hidden="true">*</span></label>
            <input
                type="text"
                id="code"
                name="code"
                class="form-input @error('code') form-input--error @enderror"
                value="{{ old('code') }}"
                placeholder="ZONA-CENTRO"
                maxlength="30"
                required
                autocomplete="off"
                @error('code') aria-invalid="true" aria-describedby="code-error" @enderror
            >
            @error('code')<span class="form-error" id="code-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="name">Nombre <span class="form-label__required" aria-hidden="true">*</span></label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-input @error('name') form-input--error @enderror"
                value="{{ old('name') }}"
                placeholder="Centro de San Salvador"
                maxlength="150"
                required
                autocomplete="off"
                @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
            >
            @error('name')<span class="form-error" id="name-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group logistics-form__full">
            <label class="form-label" for="zone_sv_municipality_id">Municipio de referencia</label>
            @include('components.municipality-select', [
                'departments' => $departments,
                'name' => 'sv_municipality_id',
                'id' => 'zone_sv_municipality_id',
                'selected' => old('sv_municipality_id'),
            ])
            @error('sv_municipality_id')<span class="form-error" id="sv_municipality_id-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group logistics-form__full"><button type="submit" class="btn btn--primary">Crear zona</button></div>
    </form>
</div>
<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Municipio</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
@forelse ($zones as $zone)
    <tr>
        <td><code>{{ $zone->code }}</code></td>
        <td>{{ $zone->name }}</td>
        <td>{{ $zone->municipality?->name ?? '—' }}</td>
        <td>
            <span class="badge badge--{{ $zone->is_active ? 'success' : 'warning' }}">
                {{ $zone->is_active ? 'Activa' : 'Inactiva' }}
            </span>
        </td>
        <td>
            <form method="POST" action="{{ route('admin.logistics.zones.destroy', $zone) }}" x-on:submit="if (!confirm('¿Eliminar esta zona?')) $event.preventDefault()" class="admin-inline-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--ghost btn--sm">Eliminar</button>
            </form>
        </td>
    </tr>
@empty
    <tr><td colspan="5" class="text-muted">Aún no hay zonas de envío.</td></tr>
@endforelse
        </tbody>
    </table>
</div>
{{ $zones->links() }}
@endsection
