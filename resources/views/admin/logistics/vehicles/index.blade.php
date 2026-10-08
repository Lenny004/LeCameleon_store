@extends('layouts.admin')

@section('title', 'Vehículos')
@section('page-title', 'Vehículos de logística')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Vehículos</h2>
        <p class="admin-page-header__subtitle">Placas, tipos y personal asignado.</p>
    </div>
    <a href="{{ route('admin.logistics.vehicles.create') }}" class="btn btn--primary">Agregar vehículo</a>
</div>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Placa</th>
                <th>Tipo</th>
                <th>Empresa</th>
                <th>Personal</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($vehicles as $vehicle)
                <tr>
                    <td><a class="admin-table__link logistics-link" href="{{ route('admin.logistics.vehicles.show', $vehicle) }}">{{ $vehicle->plate_number }}</a></td>
                    <td>{{ ucfirst($vehicle->vehicle_type->value) }}</td>
                    <td>{{ $vehicle->company?->name ?? '—' }}</td>
                    <td>{{ $vehicle->driver?->fullName() ?? '—' }}</td>
                    <td><span class="badge badge--{{ $vehicle->is_active ? 'success' : 'warning' }}">{{ $vehicle->is_active ? 'Activo' : 'Inactivo' }}</span></td>
                    <td><a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.logistics.vehicles.edit', $vehicle) }}">Editar</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">Aún no hay vehículos.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $vehicles->links() }}
@endsection
