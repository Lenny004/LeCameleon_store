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
                <label class="form-label" for="name">Company name</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $company->name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="nit">NIT</label>
                <input type="text" id="nit" name="nit" class="form-input" value="{{ old('nit', $company->nit ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="contact_name">Contact name</label>
                <input type="text" id="contact_name" name="contact_name" class="form-input" value="{{ old('contact_name', $company->contact_name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="contact_email">Contact email</label>
                <input type="email" id="contact_email" name="contact_email" class="form-input" value="{{ old('contact_email', $company->contact_email ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="contact_phone">Contact phone</label>
                <input type="text" id="contact_phone" name="contact_phone" class="form-input" value="{{ old('contact_phone', $company->contact_phone ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="municipality_id">Base municipality</label>
                <select id="municipality_id" name="municipality_id" class="form-select">
                    <option value="">— Select municipality —</option>
                    @foreach ($municipalities as $municipality)
                        <option value="{{ $municipality->id }}" @selected((string) old('municipality_id', $company->municipality_id ?? '') === (string) $municipality->id)>
                            {{ $municipality->displayName() }} ({{ $municipality->department?->name }})
                        </option>
                    @endforeach
                </select>
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
