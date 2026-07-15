@extends('layouts.admin')

@section('title', 'Logistics companies')
@section('page-title', 'Logistics companies')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Carrier companies</h2>
        <p class="admin-page-header__subtitle">Local delivery partners with NIT and base municipality.</p>
    </div>
    <a href="{{ route('admin.logistics.companies.create') }}" class="btn btn--primary">Add company</a>
</div>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Company</th>
                <th>NIT</th>
                <th>Contact</th>
                <th>Municipality</th>
                <th>Workers</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($companies as $company)
                <tr>
                    <td><a href="{{ route('admin.logistics.companies.show', $company) }}" class="logistics-link">{{ $company->name }}</a></td>
                    <td><code>{{ $company->tax_id ?: '—' }}</code></td>
                    <td>{{ $company->contact_person ?: '—' }}</td>
                    <td>{{ $company->municipality?->name ?? '—' }}</td>
                    <td>{{ $company->workers_count }}</td>
                    <td><span class="badge badge--{{ $company->is_active ? 'success' : 'warning' }}">{{ $company->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td><a href="{{ route('admin.logistics.companies.edit', $company) }}" class="btn btn--ghost btn--sm">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted">No logistics companies yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $companies->links() }}
@endsection
