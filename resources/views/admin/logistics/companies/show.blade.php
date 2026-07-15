@extends('layouts.admin')

@section('title', $company->name . ' — Logistics')
@section('page-title', $company->name)

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">{{ $company->name }}</h2>
        <p class="admin-page-header__subtitle">NIT {{ $company->tax_id ?: '—' }}</p>
    </div>
    <a href="{{ route('admin.logistics.companies.edit', $company) }}" class="btn btn--primary">Edit</a>
</div>

<div class="card logistics-detail">
    <dl class="logistics-detail__list">
        <div><dt>Contact</dt><dd>{{ $company->contact_person ?: '—' }}</dd></div>
        <div><dt>Email</dt><dd>{{ $company->email ?: '—' }}</dd></div>
        <div><dt>Phone</dt><dd>{{ $company->phone ?: '—' }}</dd></div>
        <div><dt>Municipality</dt><dd>{{ $company->municipality?->name ?? '—' }}</dd></div>
    </dl>
</div>
@endsection
