@extends('layouts.admin')

@section('title', ($company->name ?? 'Nueva empresa') . ' — Logística')
@section('page-title', isset($company) ? 'Editar empresa logística' : 'Nueva empresa logística')

@section('content')
<form method="POST" action="{{ isset($company) ? route('admin.logistics.companies.update', $company) : route('admin.logistics.companies.store') }}" class="logistics-form">
    @csrf
    @if (isset($company)) @method('PUT') @endif
    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
    <div class="card">
        <h2 class="card__title logistics-form__heading">Datos de la empresa</h2>
        <div class="logistics-form__grid">
            <div class="form-group">
                <label class="form-label" for="name">Nombre visible <span class="form-label__required" aria-hidden="true">*</span></label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-input @error('name') form-input--error @enderror"
                    value="{{ old('name', $company->name ?? '') }}"
                    placeholder="Envíos Cameleon"
                    maxlength="150"
                    required
                    autocomplete="organization"
                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                >
                @error('name')<span class="form-error" id="name-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="tax_id">NIT</label>
                <input
                    type="text"
                    id="tax_id"
                    name="tax_id"
                    class="form-input @error('tax_id') form-input--error @enderror"
                    value="{{ old('tax_id', $company->tax_id ?? '') }}"
                    placeholder="0614-010190-101-1"
                    maxlength="30"
                    autocomplete="off"
                    @error('tax_id') aria-invalid="true" aria-describedby="tax_id-error" @enderror
                >
                @error('tax_id')<span class="form-error" id="tax_id-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="contact_person">Persona de contacto</label>
                <input
                    type="text"
                    id="contact_person"
                    name="contact_person"
                    class="form-input @error('contact_person') form-input--error @enderror"
                    value="{{ old('contact_person', $company->contact_person ?? '') }}"
                    placeholder="María López"
                    maxlength="150"
                    autocomplete="name"
                    @error('contact_person') aria-invalid="true" aria-describedby="contact_person-error" @enderror
                >
                @error('contact_person')<span class="form-error" id="contact_person-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input @error('email') form-input--error @enderror"
                    value="{{ old('email', $company->email ?? '') }}"
                    placeholder="contacto@empresa.com"
                    maxlength="150"
                    inputmode="email"
                    autocomplete="email"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                >
                @error('email')<span class="form-error" id="email-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="phone">Teléfono</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    class="form-input @error('phone') form-input--error @enderror"
                    value="{{ old('phone', $company->phone ?? '') }}"
                    placeholder="7777-7777"
                    maxlength="30"
                    inputmode="tel"
                    autocomplete="tel"
                    @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror
                >
                @error('phone')<span class="form-error" id="phone-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group logistics-form__full">
                <label class="form-label" for="sv_municipality_id">Municipio base</label>
                @include('components.municipality-select', [
                    'departments' => $departments,
                    'name' => 'sv_municipality_id',
                    'id' => 'sv_municipality_id',
                    'selected' => old('sv_municipality_id', $company->sv_municipality_id ?? null),
                ])
                @error('sv_municipality_id')<span class="form-error" id="sv_municipality_id-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group logistics-form__full">
                <label class="form-label" for="notes">Notas</label>
                <textarea
                    id="notes"
                    name="notes"
                    class="form-textarea @error('notes') form-textarea--error @enderror"
                    rows="4"
                    maxlength="5000"
                    placeholder="Indicaciones para coordinar las entregas."
                    @error('notes') aria-invalid="true" aria-describedby="notes-error" @enderror
                >{{ old('notes', $company->notes ?? '') }}</textarea>
                @error('notes')<span class="form-error" id="notes-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label logistics-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $company->is_active ?? true))> Socio activo</label>
            </div>
        </div>
    </div>
    <div class="logistics-form__actions">
        <button type="submit" class="btn btn--primary">{{ isset($company) ? 'Guardar empresa' : 'Crear empresa' }}</button>
        <a href="{{ route('admin.logistics.companies.index') }}" class="btn btn--ghost">Cancelar</a>
    </div>
</form>
@endsection
