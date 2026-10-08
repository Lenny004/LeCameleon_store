@extends('layouts.admin')

@section('title', (isset($worker) ? $worker->fullName() : 'Nuevo colaborador') . ' — Logística')
@section('page-title', isset($worker) ? 'Editar colaborador' : 'Nuevo colaborador')

@section('content')
<form method="POST" action="{{ isset($worker) ? route('admin.logistics.workers.update', $worker) : route('admin.logistics.workers.store') }}" class="logistics-form">
    @csrf
    @if (isset($worker)) @method('PUT') @endif
    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
    <div class="card">
        <h2 class="card__title logistics-form__heading">Perfil del colaborador</h2>
        <div class="logistics-form__grid">
            <div class="form-group">
                <label class="form-label" for="first_name">Nombre <span class="form-label__required" aria-hidden="true">*</span></label>
                <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    class="form-input @error('first_name') form-input--error @enderror"
                    value="{{ old('first_name', $worker->first_name ?? '') }}"
                    placeholder="María"
                    maxlength="100"
                    required
                    autocomplete="given-name"
                    @error('first_name') aria-invalid="true" aria-describedby="first_name-error" @enderror
                >
                @error('first_name')<span class="form-error" id="first_name-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="last_name">Apellido <span class="form-label__required" aria-hidden="true">*</span></label>
                <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    class="form-input @error('last_name') form-input--error @enderror"
                    value="{{ old('last_name', $worker->last_name ?? '') }}"
                    placeholder="López"
                    maxlength="100"
                    required
                    autocomplete="family-name"
                    @error('last_name') aria-invalid="true" aria-describedby="last_name-error" @enderror
                >
                @error('last_name')<span class="form-error" id="last_name-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="document_id">DUI</label>
                <input
                    type="text"
                    id="document_id"
                    name="document_id"
                    class="form-input @error('document_id') form-input--error @enderror"
                    value="{{ old('document_id', $worker->document_id ?? '') }}"
                    placeholder="01234567-8"
                    maxlength="20"
                    autocomplete="off"
                    @error('document_id') aria-invalid="true" aria-describedby="document_id-error" @enderror
                >
                @error('document_id')<span class="form-error" id="document_id-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="role">Rol <span class="form-label__required" aria-hidden="true">*</span></label>
                <select
                    id="role"
                    name="role"
                    class="form-select @error('role') form-select--error @enderror"
                    required
                    @error('role') aria-invalid="true" aria-describedby="role-error" @enderror
                >
                    <option value="" disabled {{ old('role', $worker->role->value ?? '') === '' ? 'selected' : '' }}>Selecciona un rol</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected(old('role', $worker->role->value ?? '') === $role->value)>
                            {{ ['collector' => 'Recolector', 'driver' => 'Conductor', 'dispatcher' => 'Despachador', 'supervisor' => 'Supervisor'][$role->value] }}
                        </option>
                    @endforeach
                </select>
                @error('role')<span class="form-error" id="role-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="logistics_company_id">Empresa</label>
                <select
                    id="logistics_company_id"
                    name="logistics_company_id"
                    class="form-select @error('logistics_company_id') form-select--error @enderror"
                    @error('logistics_company_id') aria-invalid="true" aria-describedby="logistics_company_id-error" @enderror
                >
                    <option value="">— Ninguna —</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @selected((string) old('logistics_company_id', $worker->logistics_company_id ?? '') === (string) $company->id)>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
                @error('logistics_company_id')<span class="form-error" id="logistics_company_id-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label logistics-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $worker->is_active ?? true))> Colaborador activo</label>
            </div>
        </div>
    </div>
    <div class="logistics-form__actions">
        <button type="submit" class="btn btn--primary">{{ isset($worker) ? 'Guardar colaborador' : 'Crear colaborador' }}</button>
        <a href="{{ route('admin.logistics.workers.index') }}" class="btn btn--ghost">Cancelar</a>
    </div>
</form>
@endsection
