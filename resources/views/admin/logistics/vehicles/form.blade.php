@extends('layouts.admin')

@section('title', (isset($vehicle) ? $vehicle->plate_number : 'New vehicle') . ' — Logistics')
@section('page-title', isset($vehicle) ? 'Edit vehicle' : 'New vehicle')

@section('content')
<form method="POST"
      action="{{ isset($vehicle) ? route('admin.logistics.vehicles.update', $vehicle) : route('admin.logistics.vehicles.store') }}"
      class="logistics-form">
    @csrf
    @if (isset($vehicle))
        @method('PUT')
    @endif

    <div class="card">
        <h2 class="card__title logistics-form__heading">Vehicle details</h2>
        <div class="logistics-form__grid">
            <div class="form-group">
                <label class="form-label" for="plate_number">License plate</label>
                <input type="text" id="plate_number" name="plate_number" class="form-input" value="{{ old('plate_number', $vehicle->plate_number ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="vehicle_type">Type</label>
                <select id="vehicle_type" name="vehicle_type" class="form-select" required>
                    @foreach ($types as $type)
                        <option value="{{ $type->value }}" @selected(old('vehicle_type', $vehicle->vehicle_type->value ?? '') === $type->value)>{{ ucfirst($type->value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="logistics_company_id">Company</label>
                <select id="logistics_company_id" name="logistics_company_id" class="form-select" required>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @selected((string) old('logistics_company_id', $vehicle->logistics_company_id ?? '') === (string) $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="logistics_worker_id">Assigned worker</label>
                <select id="logistics_worker_id" name="logistics_worker_id" class="form-select">
                    <option value="">— Unassigned —</option>
                    @foreach ($workers as $worker)
                        <option value="{{ $worker->id }}" @selected((string) old('logistics_worker_id', $vehicle->logistics_worker_id ?? '') === (string) $worker->id)>{{ $worker->fullName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label logistics-checkbox">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $vehicle->is_active ?? true))>
                    Active vehicle
                </label>
            </div>
        </div>
    </div>

    <div class="logistics-form__actions">
        <button type="submit" class="btn btn--primary">{{ isset($vehicle) ? 'Save vehicle' : 'Create vehicle' }}</button>
        <a href="{{ route('admin.logistics.vehicles.index') }}" class="btn btn--ghost">Cancel</a>
    </div>
</form>
@endsection
