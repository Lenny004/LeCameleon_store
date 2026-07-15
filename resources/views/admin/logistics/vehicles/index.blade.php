@extends('layouts.admin')

@section('title', 'Vehicles')
@section('page-title', 'Logistics vehicles')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Vehicles</h2>
        <p class="admin-page-header__subtitle">Fleet plates, types, and assigned workers.</p>
    </div>
    <a href="{{ route('admin.logistics.vehicles.create') }}" class="btn btn--primary">Add vehicle</a>
</div>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Plate</th>
                <th>Type</th>
                <th>Company</th>
                <th>Worker</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($vehicles as $vehicle)
                <tr>
                    <td><a href="{{ route('admin.logistics.vehicles.show', $vehicle) }}" class="logistics-link">{{ $vehicle->plate_number }}</a></td>
                    <td>{{ ucfirst($vehicle->vehicle_type->value) }}</td>
                    <td>{{ $vehicle->company?->name ?? '—' }}</td>
                    <td>{{ $vehicle->driver?->fullName() ?? '—' }}</td>
                    <td><span class="badge badge--{{ $vehicle->is_active ? 'success' : 'warning' }}">{{ $vehicle->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td><a href="{{ route('admin.logistics.vehicles.edit', $vehicle) }}" class="btn btn--ghost btn--sm">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">No vehicles yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $vehicles->links() }}
@endsection
