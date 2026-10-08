@extends('layouts.admin')

@section('title', (isset($vehicle) ? $vehicle->plate_number : 'Nuevo vehículo') . ' — Logística')
@section('page-title', isset($vehicle) ? 'Editar vehículo' : 'Nuevo vehículo')

@section('content')
<form method="POST" action="{{ isset($vehicle) ? route('admin.logistics.vehicles.update', $vehicle) : route('admin.logistics.vehicles.store') }}" class="logistics-form">
    @csrf
    @if (isset($vehicle)) @method('PUT') @endif
    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
    <div class="card">
        <h2 class="card__title logistics-form__heading">Datos del vehículo</h2>
        <div class="logistics-form__grid">
            <div class="form-group">
                <label class="form-label" for="plate_number">Placa <span class="form-label__required" aria-hidden="true">*</span></label>
                <input
                    type="text"
                    id="plate_number"
                    name="plate_number"
                    class="form-input @error('plate_number') form-input--error @enderror"
                    value="{{ old('plate_number', $vehicle->plate_number ?? '') }}"
                    placeholder="P123-456"
                    maxlength="20"
                    required
                    autocomplete="off"
                    @error('plate_number') aria-invalid="true" aria-describedby="plate_number-error" @enderror
                >
                @error('plate_number')<span class="form-error" id="plate_number-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="vehicle_type">Tipo <span class="form-label__required" aria-hidden="true">*</span></label>
                <select
                    id="vehicle_type"
                    name="vehicle_type"
                    class="form-select @error('vehicle_type') form-select--error @enderror"
                    required
                    @error('vehicle_type') aria-invalid="true" aria-describedby="vehicle_type-error" @enderror
                >
                    <option value="" disabled {{ old('vehicle_type', $vehicle->vehicle_type->value ?? '') === '' ? 'selected' : '' }}>Selecciona un tipo</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->value }}" @selected(old('vehicle_type', $vehicle->vehicle_type->value ?? '') === $type->value)>
                            {{ ['motorcycle' => 'Motocicleta', 'van' => 'Panel', 'truck' => 'Camión', 'bicycle' => 'Bicicleta', 'car' => 'Automóvil'][$type->value] }}
                        </option>
                    @endforeach
                </select>
                @error('vehicle_type')<span class="form-error" id="vehicle_type-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="logistics_company_id">Empresa <span class="form-label__required" aria-hidden="true">*</span></label>
                <select
                    id="logistics_company_id"
                    name="logistics_company_id"
                    class="form-select @error('logistics_company_id') form-select--error @enderror"
                    required
                    @error('logistics_company_id') aria-invalid="true" aria-describedby="logistics_company_id-error" @enderror
                >
                    <option value="" disabled {{ old('logistics_company_id', $vehicle->logistics_company_id ?? '') === '' ? 'selected' : '' }}>Selecciona una empresa</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @selected((string) old('logistics_company_id', $vehicle->logistics_company_id ?? '') === (string) $company->id)>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
                @error('logistics_company_id')<span class="form-error" id="logistics_company_id-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="logistics_worker_id">Colaborador asignado</label>
                <select
                    id="logistics_worker_id"
                    name="logistics_worker_id"
                    class="form-select @error('logistics_worker_id') form-select--error @enderror"
                    @error('logistics_worker_id') aria-invalid="true" aria-describedby="logistics_worker_id-error" @enderror
                >
                    <option value="">— Sin asignar —</option>
                    @foreach ($workers as $worker)
                        <option value="{{ $worker->id }}" @selected((string) old('logistics_worker_id', $vehicle->logistics_worker_id ?? '') === (string) $worker->id)>
                            {{ $worker->fullName() }}
                        </option>
                    @endforeach
                </select>
                @error('logistics_worker_id')<span class="form-error" id="logistics_worker_id-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label logistics-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $vehicle->is_active ?? true))> Vehículo activo</label>
            </div>
        </div>
    </div>
    <div class="logistics-form__actions">
        <button type="submit" class="btn btn--primary">{{ isset($vehicle) ? 'Guardar vehículo' : 'Crear vehículo' }}</button>
        <a href="{{ route('admin.logistics.vehicles.index') }}" class="btn btn--ghost">Cancelar</a>
    </div>
</form>
@endsection
