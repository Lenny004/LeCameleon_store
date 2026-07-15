@extends('layouts.admin')

@section('title', $worker->full_name . ' — Workers')
@section('page-title', $worker->full_name)

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">{{ $worker->full_name }}</h2>
        <p class="admin-page-header__subtitle">DUI {{ $worker->dui }} · {{ ucfirst($worker->role->value) }}</p>
    </div>
    <div class="logistics-form__actions">
        <a href="{{ route('admin.logistics.workers.edit', $worker) }}" class="btn btn--primary">Edit</a>
        <form method="POST" action="{{ route('admin.logistics.workers.destroy', $worker) }}" onsubmit="return confirm('Delete this worker?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn--ghost">Delete</button>
        </form>
    </div>
</div>

<div class="logistics-detail">
    <div class="card">
        <dl class="logistics-detail__list">
            <div><dt>Company</dt><dd><a href="{{ route('admin.logistics.companies.show', $worker->company) }}" class="logistics-link">{{ $worker->company->name }}</a></dd></div>
            <div><dt>Phone</dt><dd>{{ $worker->phone ?: '—' }}</dd></div>
            <div><dt>Email</dt><dd>{{ $worker->email ?: '—' }}</dd></div>
            <div><dt>Status</dt><dd>{{ $worker->is_active ? 'Active' : 'Inactive' }}</dd></div>
        </dl>
    </div>

    <div class="card">
        <h2 class="card__title logistics-form__heading">Assigned vehicles</h2>
        @if ($worker->vehicles->isEmpty())
            <p class="text-muted">No vehicles assigned.</p>
        @else
            <ul class="logistics-detail__bullets">
                @foreach ($worker->vehicles as $vehicle)
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
