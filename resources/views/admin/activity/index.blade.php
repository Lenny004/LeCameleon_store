@extends('layouts.admin')

@section('title', 'Registro de actividad')
@section('page-title', 'Registro de actividad')
@section('page-subtitle', 'Últimas acciones administrativas')

@section('content')
<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Cuándo</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Elemento</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ $log->user?->email ?? '—' }}</td>
                    <td><code>{{ $log->action }}</code></td>
                    <td>
                        @if ($log->subject_type)
                            {{ class_basename($log->subject_type) }} #{{ Str::limit($log->subject_id, 8, '') }}
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $log->ip_address ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Aún no hay actividad registrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
