@extends('layouts.admin')

@section('title', 'Municipios')
@section('page-title', 'Municipios de El Salvador')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Municipios</h2>
        <p class="admin-page-header__subtitle">14 departamentos (solo lectura) · edita costo base y coordenadas.</p>
    </div>
</div>

@foreach ($departments as $department)
    <section class="logistics-department">
        <h3 class="logistics-department__title">{{ $department->name }} <span class="text-muted">({{ $department->code }})</span></h3>
        <div class="table-wrap">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Municipio</th>
                        <th>Código</th>
                        <th>Costo base</th>
                        <th>Lat. / long.</th>
                        <th>Estado</th>
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
                                    {{ $municipality->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.logistics.municipalities.edit', $municipality) }}">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endforeach
@endsection
