@extends('layouts.admin')

@section('title', ($vehicle->plate ?? 'New vehicle') . ' — Logistics')
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
                <label class="form-label" for="plate">License plate</label>
                <input type="text" id="plate" name="plate" class="form-input" value="{{ old('plate', $vehicle->plate ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="type">Type</label>
                <select id="type" name="type" class="form-select" required>
                    @foreach ($types as $type)
                        <option value="{{ $type->value }}" @selected(old('type', $vehicle->type->value ?? '') === $type->value)>
                            {{ ucfirst($type->value) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="logistics_company_id">Company</label>
                <select id="logistics_company_id" name="logistics_company_id" class="form-select" required>
                    <option value="">— Select company —</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @selected((string) old('logistics_company_id', $vehicle->logistics_company_id ?? '') === (string) $company->id)>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="logistics_worker_id">Assigned worker</label>
                <select id="logistics_worker_id" name="logistics_worker_id" class="form-select">
                    <option value="">— Unassigned —</option>
                    @foreach ($workers as $worker)
                        <option value="{{ $worker->id }}" @selected((string) old('logistics_worker_id', $vehicle->logistics_worker_id ?? '') === (string) $worker->id)>
                            {{ $worker->full_name }} ({{ $worker->company?->name }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group logistics-form__full">
                <label class="form-label" for="description">Description</label>
                <input type="text" id="description" name="description" class="form-input" value="{{ old('description', $vehicle->description ?? '') }}">
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
