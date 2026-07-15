@extends('layouts.admin')

@section('title', 'Return requests')
@section('page-title', 'Return requests')
@section('page-subtitle', 'Review customer return submissions')

@section('content')
<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Item</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($returnRequests as $returnRequest)
                <tr>
                    <td>#{{ $returnRequest->order?->number }}</td>
                    <td>{{ $returnRequest->user?->email ?? '—' }}</td>
                    <td>{{ $returnRequest->orderItem?->name ?? 'Full order' }}</td>
                    <td>{{ Str::limit($returnRequest->reason, 50) }}</td>
                    <td>
                        <span class="badge badge--{{ $returnRequest->status->value === 'approved' ? 'success' : ($returnRequest->status->value === 'denied' ? 'error' : 'warning') }}">
                            {{ ucfirst($returnRequest->status->value) }}
                        </span>
                    </td>
                    <td>{{ $returnRequest->created_at?->format('Y-m-d') }}</td>
                    <td>
                        @if ($returnRequest->status->value === 'pending')
                            <div class="table__actions" style="display:flex;gap:var(--space-xs);">
                                @if (Route::has('admin.return-requests.approve'))
                                    <form method="POST" action="{{ route('admin.return-requests.approve', $returnRequest) }}" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn--ghost btn--sm">Approve</button>
                                    </form>
                                @endif
                                @if (Route::has('admin.return-requests.deny'))
                                    <form method="POST" action="{{ route('admin.return-requests.deny', $returnRequest) }}" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn--ghost btn--sm">Deny</button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-muted">No return requests yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($returnRequests->hasPages())
    <div style="margin-top:var(--space-lg);">
        {{ $returnRequests->links() }}
    </div>
@endif
@endsection
