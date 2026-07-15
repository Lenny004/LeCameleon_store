@extends('layouts.admin')

@section('title', $vehicle->plate . ' — Vehicles')
@section('page-title', $vehicle->plate)

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">{{ $vehicle->plate }}</h2>
        <p class="admin-page-header__subtitle">{{ ucfirst($vehicle->type->value) }}</p>
    </div>
    <div class="logistics-form__actions">
        <a href="{{ route('admin.logistics.vehicles.edit', $vehicle) }}" class="btn btn--primary">Edit</a>
        <form method="POST" action="{{ route('admin.logistics.vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Delete this vehicle?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn--ghost">Delete</button>
        </form>
    </div>
</div>

<div class="card logistics-detail">
    <dl class="logistics-detail__list">
        <div><dt>Company</dt><dd><a href="{{ route('admin.logistics.companies.show', $vehicle->company) }}" class="logistics-link">{{ $vehicle->company->name }}</a></dd></div>
        <div><dt>Assigned worker</dt>
            <dd>
                @if ($vehicle->worker)
                    <a href="{{ route('admin.logistics.workers.show', $vehicle->worker) }}" class="logistics-link">{{ $vehicle->worker->full_name }}</a>
                @else
                    —
                @endif
            </dd>
        </div>
        <div><dt>Description</dt><dd>{{ $vehicle->description ?: '—' }}</dd></div>
        <div><dt>Status</dt><dd>{{ $vehicle->is_active ? 'Active' : 'Inactive' }}</dd></div>
    </dl>
</div>
@endsection
