@extends('layouts.admin')

@section('title', 'Offers')
@section('page-title', 'Offers')
@section('page-subtitle', 'Review buyer price proposals')

@section('content')
<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Customer</th>
                <th>Offer</th>
                <th>Counter</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($offers as $offer)
                <tr>
                    <td>
                        <a href="{{ $offer->product ? url('/shop/'.$offer->product->slug) : '#' }}">
                            {{ $offer->product?->name ?? '—' }}
                        </a>
                        @if ($offer->product)
                            <br><span class="text-muted text-small">List: ${{ number_format((float) $offer->product->price, 2) }}</span>
                        @endif
                    </td>
                    <td>{{ $offer->user?->email ?? '—' }}</td>
                    <td>${{ number_format((float) $offer->amount, 2) }}</td>
                    <td>
                        @if ($offer->counter_amount)
                            ${{ number_format((float) $offer->counter_amount, 2) }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @php
                            $statusBadge = match ($offer->status->value) {
                                'accepted' => 'success',
                                'declined' => 'error',
                                'countered' => 'primary',
                                default => 'warning',
                            };
                        @endphp
                        <span class="badge badge--{{ $statusBadge }}">
                            {{ ucfirst($offer->status->value) }}
                        </span>
                    </td>
                    <td>{{ $offer->created_at?->format('Y-m-d') }}</td>
                    <td>
                        @if ($offer->status->value === 'pending')
                            <div class="table__actions" style="display:flex;flex-direction:column;gap:var(--space-xs);min-width:12rem;">
                                @if (Route::has('admin.offers.accept'))
                                    <form method="POST" action="{{ route('admin.offers.accept', $offer) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="admin_notes" class="form-input form-input--sm" placeholder="Notes (optional)" style="margin-bottom:var(--space-xs);">
                                        <button type="submit" class="btn btn--ghost btn--sm">Accept</button>
                                    </form>
                                @endif
                                @if (Route::has('admin.offers.decline'))
                                    <form method="POST" action="{{ route('admin.offers.decline', $offer) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn--ghost btn--sm">Decline</button>
                                    </form>
                                @endif
                                @if (Route::has('admin.offers.counter'))
                                    <form method="POST" action="{{ route('admin.offers.counter', $offer) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="counter_amount" class="form-input form-input--sm" step="0.01" min="1" placeholder="Counter $" required style="margin-bottom:var(--space-xs);">
                                        <input type="text" name="admin_notes" class="form-input form-input--sm" placeholder="Notes (optional)" style="margin-bottom:var(--space-xs);">
                                        <button type="submit" class="btn btn--ghost btn--sm">Counter</button>
                                    </form>
                                @endif
                            </div>
                        @elseif ($offer->admin_notes)
                            <span class="text-muted text-small">{{ Str::limit($offer->admin_notes, 40) }}</span>
                        @endif
                        @if ($offer->message)
                            <p class="text-muted text-small" style="margin-top:var(--space-xs);">"{{ Str::limit($offer->message, 60) }}"</p>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-muted">No offers yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($offers->hasPages())
    <div style="margin-top:var(--space-lg);">
        {{ $offers->links() }}
    </div>
@endif
@endsection
