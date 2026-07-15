@extends('layouts.admin')

@section('title', 'Workers')
@section('page-title', 'Logistics workers')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Workers</h2>
        <p class="admin-page-header__subtitle">Couriers and drivers with DUI and company assignment.</p>
    </div>
    <a href="{{ route('admin.logistics.workers.create') }}" class="btn btn--primary">Add worker</a>
</div>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>DUI</th>
                <th>Role</th>
                <th>Company</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($workers as $worker)
                <tr>
                    <td>
                        <a href="{{ route('admin.logistics.workers.show', $worker) }}" class="logistics-link">{{ $worker->full_name }}</a>
                    </td>
                    <td><code>{{ $worker->dui }}</code></td>
                    <td>{{ ucfirst($worker->role->value) }}</td>
                    <td>{{ $worker->company?->name ?? '—' }}</td>
                    <td>
                        <span class="badge badge--{{ $worker->is_active ? 'success' : 'warning' }}">
                            {{ $worker->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.logistics.workers.edit', $worker) }}" class="btn btn--ghost btn--sm">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-muted">No workers yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $workers->links() }}
@endsection
