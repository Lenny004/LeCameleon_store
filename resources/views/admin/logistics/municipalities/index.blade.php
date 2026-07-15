@extends('layouts.admin')

@section('title', 'Municipalities')
@section('page-title', 'SV municipalities')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Municipalities</h2>
        <p class="admin-page-header__subtitle">14 departments (read-only) · edit base shipping cost and coordinates.</p>
    </div>
</div>

@foreach ($departments as $department)
    <section class="logistics-department">
        <h3 class="logistics-department__title">{{ $department->name }} <span class="text-muted">({{ $department->code }})</span></h3>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Municipality</th>
                        <th>Code</th>
                        <th>Base cost</th>
                        <th>Lat / Lng</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($department->municipalities as $municipality)
                        <tr>
                            <td>{{ $municipality->name }}</td>
                            <td><code>{{ $municipality->code }}</code></td>
                            <td>${{ number_format((float) $municipality->base_shipping_cost, 2) }}</td>
                            <td class="text-muted">
                                @if ($municipality->latitude && $municipality->longitude)
                                    {{ $municipality->latitude }}, {{ $municipality->longitude }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="badge badge--{{ $municipality->is_active ? 'success' : 'warning' }}">
                                    {{ $municipality->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.logistics.municipalities.edit', $municipality) }}" class="btn btn--ghost btn--sm">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endforeach
@endsection
