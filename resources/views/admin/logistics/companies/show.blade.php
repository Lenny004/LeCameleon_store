@extends('layouts.admin')

@section('title', $company->name . ' — Logistics')
@section('page-title', $company->name)

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">{{ $company->name }}</h2>
        <p class="admin-page-header__subtitle">NIT {{ $company->nit }}</p>
    </div>
    <div class="logistics-form__actions">
        <a href="{{ route('admin.logistics.companies.edit', $company) }}" class="btn btn--primary">Edit</a>
        <form method="POST" action="{{ route('admin.logistics.companies.destroy', $company) }}" onsubmit="return confirm('Delete this company?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn--ghost">Delete</button>
        </form>
    </div>
</div>

<div class="logistics-detail">
    <div class="card">
        <h2 class="card__title logistics-form__heading">Contact</h2>
        <dl class="logistics-detail__list">
            <div><dt>Contact</dt><dd>{{ $company->contact_name }}</dd></div>
            <div><dt>Email</dt><dd>{{ $company->contact_email ?: '—' }}</dd></div>
            <div><dt>Phone</dt><dd>{{ $company->contact_phone ?: '—' }}</dd></div>
            <div><dt>Municipality</dt><dd>{{ $company->municipality?->displayName() ?? '—' }}</dd></div>
            <div><dt>Status</dt><dd>{{ $company->is_active ? 'Active' : 'Inactive' }}</dd></div>
        </dl>
        @if ($company->notes)
            <p class="text-muted logistics-detail__notes">{{ $company->notes }}</p>
        @endif
    </div>

    <div class="card">
        <h2 class="card__title logistics-form__heading">Workers ({{ $company->workers->count() }})</h2>
        @if ($company->workers->isEmpty())
            <p class="text-muted">No workers assigned.</p>
        @else
            <ul class="logistics-detail__bullets">
                @foreach ($company->workers as $worker)
                    <li>
                        <a href="{{ route('admin.logistics.workers.show', $worker) }}" class="logistics-link">{{ $worker->full_name }}</a>
                        — {{ ucfirst($worker->role->value) }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="card">
        <h2 class="card__title logistics-form__heading">Vehicles ({{ $company->vehicles->count() }})</h2>
        @if ($company->vehicles->isEmpty())
            <p class="text-muted">No vehicles registered.</p>
        @else
            <ul class="logistics-detail__bullets">
                @foreach ($company->vehicles as $vehicle)
                    <li>
                        <a href="{{ route('admin.logistics.vehicles.show', $vehicle) }}" class="logistics-link">{{ $vehicle->plate }}</a>
                        — {{ ucfirst($vehicle->type->value) }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
