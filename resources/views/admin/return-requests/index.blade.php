@extends('layouts.admin')

@section('title', 'Solicitudes de devolución')
@section('page-title', 'Solicitudes de devolución')
@section('page-subtitle', 'Revisa las solicitudes enviadas por clientes')

@section('content')
<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Pedido</th>
                <th>Cliente</th>
                <th>Artículo</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($returnRequests as $returnRequest)
                <tr>
                    <td>#{{ $returnRequest->order?->number }}</td>
                    <td>{{ $returnRequest->user?->email ?? '—' }}</td>
                    <td>{{ $returnRequest->orderItem?->name ?? 'Pedido completo' }}</td>
                    <td>{{ Str::limit($returnRequest->reason, 50) }}</td>
                    <td>
                        <span class="badge badge--{{ match ($returnRequest->status->value) {
                            'approved' => 'success',
                            'denied' => 'danger',
                            'refunded' => 'success',
                            default => 'warning',
                        } }}">
                            {{ ['pending' => 'Pendiente', 'approved' => 'Aprobada', 'denied' => 'Rechazada', 'refunded' => 'Reembolsada'][$returnRequest->status->value] }}
                        </span>
                    </td>
                    <td>{{ $returnRequest->created_at?->format('Y-m-d') }}</td>
                    <td>
                        @if ($returnRequest->status->value === 'pending')
                            <div class="admin-table__actions">
                                @if (Route::has('admin.return-requests.approve'))
                                    <form method="POST" action="{{ route('admin.return-requests.approve', $returnRequest) }}" class="admin-inline-form">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn--ghost btn--sm">Aprobar</button>
                                    </form>
                                @endif
                                @if (Route::has('admin.return-requests.deny'))
                                    <form method="POST" action="{{ route('admin.return-requests.deny', $returnRequest) }}" class="admin-inline-form">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn--ghost btn--sm">Rechazar</button>
                                    </form>
                                @endif
                            </div>
                        @elseif ($returnRequest->status->value === 'approved' && Route::has('admin.return-requests.refund'))
                            <form method="POST" action="{{ route('admin.return-requests.refund', $returnRequest) }}" class="admin-inline-form">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn--ghost btn--sm">Marcar como reembolsada</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-muted">Aún no hay solicitudes de devolución.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($returnRequests->hasPages())
    <div class="admin-pagination">
        {{ $returnRequests->links() }}
    </div>
@endif
@endsection
