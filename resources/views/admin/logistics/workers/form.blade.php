@extends('layouts.admin')

@section('title', ($worker->full_name ?? 'New worker') . ' — Logistics')
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
                <label class="form-label" for="full_name">Full name</label>
                <input type="text" id="full_name" name="full_name" class="form-input" value="{{ old('full_name', $worker->full_name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="dui">DUI</label>
                <input type="text" id="dui" name="dui" class="form-input" value="{{ old('dui', $worker->dui ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="role">Role</label>
                <select id="role" name="role" class="form-select" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected(old('role', $worker->role->value ?? '') === $role->value)>
                            {{ ucfirst($role->value) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="logistics_company_id">Company</label>
                <select id="logistics_company_id" name="logistics_company_id" class="form-select" required>
                    <option value="">— Select company —</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @selected((string) old('logistics_company_id', $worker->logistics_company_id ?? '') === (string) $company->id)>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="phone">Phone</label>
                <input type="text" id="phone" name="phone" class="form-input" value="{{ old('phone', $worker->phone ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $worker->email ?? '') }}">
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
