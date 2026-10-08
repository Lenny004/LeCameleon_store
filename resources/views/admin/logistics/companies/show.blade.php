@extends('layouts.admin')

@section('title', $company->name . ' — Logística')
@section('page-title', $company->name)

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">{{ $company->name }}</h2>
        <p class="admin-page-header__subtitle">NIT {{ $company->tax_id ?: '—' }}</p>
    </div>
    <a href="{{ route('admin.logistics.companies.edit', $company) }}" class="btn btn--primary">Editar</a>
</div>

<div class="card logistics-detail">
    <dl class="logistics-detail__list">
        <div><dt class="logistics-detail__term">Contacto</dt><dd class="logistics-detail__value">{{ $company->contact_person ?: '—' }}</dd></div>
        <div><dt class="logistics-detail__term">Correo</dt><dd class="logistics-detail__value">{{ $company->email ?: '—' }}</dd></div>
        <div><dt class="logistics-detail__term">Teléfono</dt><dd class="logistics-detail__value">{{ $company->phone ?: '—' }}</dd></div>
        <div><dt class="logistics-detail__term">Municipio</dt><dd class="logistics-detail__value">{{ $company->municipality?->name ?? '—' }}</dd></div>
    </dl>
</div>
@endsection
