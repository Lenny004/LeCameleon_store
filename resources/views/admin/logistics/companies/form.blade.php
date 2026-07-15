@extends('layouts.admin')

@section('title', ($company->name ?? 'New company') . ' — Logistics')
@section('page-title', isset($company) ? 'Edit company' : 'New company')

@section('content')
<form method="POST"
      action="{{ isset($company) ? route('admin.logistics.companies.update', $company) : route('admin.logistics.companies.store') }}"
      class="logistics-form">
    @csrf
    @if (isset($company))
        @method('PUT')
    @endif

    <div class="card">
        <h2 class="card__title logistics-form__heading">Company details</h2>
        <div class="logistics-form__grid">
            <div class="form-group">
                <label class="form-label" for="name">Display name</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $company->name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="tax_id">NIT</label>
                <input type="text" id="tax_id" name="tax_id" class="form-input" value="{{ old('tax_id', $company->tax_id ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="contact_person">Contact person</label>
                <input type="text" id="contact_person" name="contact_person" class="form-input" value="{{ old('contact_person', $company->contact_person ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $company->email ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="phone">Phone</label>
                <input type="text" id="phone" name="phone" class="form-input" value="{{ old('phone', $company->phone ?? '') }}">
            </div>
            <div class="form-group logistics-form__full">
                <label class="form-label" for="sv_municipality_id">Base municipality</label>
                @include('components.municipality-select', [
                    'departments' => $departments,
                    'name' => 'sv_municipality_id',
                    'id' => 'sv_municipality_id',
                    'selected' => old('sv_municipality_id', $company->sv_municipality_id ?? null),
                ])
            </div>
            <div class="form-group logistics-form__full">
                <label class="form-label" for="notes">Notes</label>
                <textarea id="notes" name="notes" class="form-textarea">{{ old('notes', $company->notes ?? '') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label logistics-checkbox">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $company->is_active ?? true))>
                    Active partner
                </label>
            </div>
        </div>
    </div>

    <div class="logistics-form__actions">
        <button type="submit" class="btn btn--primary">{{ isset($company) ? 'Save company' : 'Create company' }}</button>
        <a href="{{ route('admin.logistics.companies.index') }}" class="btn btn--ghost">Cancel</a>
    </div>
</form>
@endsection
