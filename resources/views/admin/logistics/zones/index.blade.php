@extends('layouts.admin')

@section('title', 'Shipping zones')
@section('page-title', 'Shipping zones')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Shipping zones</h2>
        <p class="admin-page-header__subtitle">Zone anchors for origin → destination rate matrix.</p>
    </div>
    <a href="{{ route('admin.logistics.zones.rates-matrix') }}" class="btn btn--primary">Rates matrix</a>
</div>

<div class="card" style="margin-bottom:var(--space-xl);">
    <h2 class="card__title logistics-form__heading">Add zone</h2>
    <form method="POST" action="{{ route('admin.logistics.zones.store') }}" class="logistics-form__grid">
        @csrf
        <div class="form-group">
            <label class="form-label" for="code">Code</label>
            <input type="text" id="code" name="code" class="form-input" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="name">Name</label>
            <input type="text" id="name" name="name" class="form-input" required>
        </div>
        <div class="form-group logistics-form__full">
            <label class="form-label" for="zone_sv_municipality_id">Anchor municipality</label>
            @include('components.municipality-select', [
                'departments' => $departments,
                'name' => 'sv_municipality_id',
                'id' => 'zone_sv_municipality_id',
            ])
        </div>
        <div class="form-group logistics-form__full">
            <button type="submit" class="btn btn--primary">Create zone</button>
        </div>
    </form>
</div>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Municipality</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($zones as $zone)
                <tr>
                    <td><code>{{ $zone->code }}</code></td>
                    <td>{{ $zone->name }}</td>
                    <td>{{ $zone->municipality?->name ?? '—' }}</td>
                    <td><span class="badge badge--{{ $zone->is_active ? 'success' : 'warning' }}">{{ $zone->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.logistics.zones.destroy', $zone) }}" onsubmit="return confirm('Remove zone?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">No shipping zones yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $zones->links() }}
@endsection
