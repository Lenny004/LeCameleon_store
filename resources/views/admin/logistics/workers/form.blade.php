@extends('layouts.admin')

@section('title', (isset($worker) ? $worker->fullName() : 'New worker') . ' — Logistics')
@section('page-title', isset($worker) ? 'Edit worker' : 'New worker')

@section('content')
<form method="POST"
      action="{{ isset($worker) ? route('admin.logistics.workers.update', $worker) : route('admin.logistics.workers.store') }}"
      class="logistics-form">
    @csrf
    @if (isset($worker))
        @method('PUT')
    @endif

    <div class="card">
        <h2 class="card__title logistics-form__heading">Worker profile</h2>
        <div class="logistics-form__grid">
            <div class="form-group">
                <label class="form-label" for="first_name">First name</label>
                <input type="text" id="first_name" name="first_name" class="form-input" value="{{ old('first_name', $worker->first_name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="last_name">Last name</label>
                <input type="text" id="last_name" name="last_name" class="form-input" value="{{ old('last_name', $worker->last_name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="document_id">DUI</label>
                <input type="text" id="document_id" name="document_id" class="form-input" value="{{ old('document_id', $worker->document_id ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="role">Role</label>
                <select id="role" name="role" class="form-select" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected(old('role', $worker->role->value ?? '') === $role->value)>{{ ucfirst($role->value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="logistics_company_id">Company</label>
                <select id="logistics_company_id" name="logistics_company_id" class="form-select">
                    <option value="">— Select company —</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @selected((string) old('logistics_company_id', $worker->logistics_company_id ?? '') === (string) $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label logistics-checkbox">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $worker->is_active ?? true))>
                    Active worker
                </label>
            </div>
        </div>
    </div>

    <div class="logistics-form__actions">
        <button type="submit" class="btn btn--primary">{{ isset($worker) ? 'Save worker' : 'Create worker' }}</button>
        <a href="{{ route('admin.logistics.workers.index') }}" class="btn btn--ghost">Cancel</a>
    </div>
</form>
@endsection
