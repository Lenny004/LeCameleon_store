@extends('layouts.admin')

@section('title', 'Personal')
@section('page-title', 'Personal de logística')

@section('content')
@php $roleLabels = ['driver' => 'Conductor', 'courier' => 'Mensajero', 'dispatcher' => 'Despachador']; @endphp
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Personal</h2>
        <p class="admin-page-header__subtitle">Mensajeros y conductores con DUI y empresa asignada.</p>
    </div>
    <a href="{{ route('admin.logistics.workers.create') }}" class="btn btn--primary">Agregar personal</a>
</div>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>DUI</th>
                <th>Rol</th>
                <th>Empresa</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($workers as $worker)
                <tr>
                    <td><a class="admin-table__link logistics-link" href="{{ route('admin.logistics.workers.show', $worker) }}">{{ $worker->fullName() }}</a></td>
                    <td><code>{{ $worker->document_id ?: '—' }}</code></td>
                    <td>{{ $roleLabels[$worker->role->value] ?? $worker->role->value }}</td>
                    <td>{{ $worker->company?->name ?? '—' }}</td>
                    <td><span class="badge badge--{{ $worker->is_active ? 'success' : 'warning' }}">{{ $worker->is_active ? 'Activo' : 'Inactivo' }}</span></td>
                    <td><a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.logistics.workers.edit', $worker) }}">Editar</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">Aún no hay personal.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $workers->links() }}
@endsection
