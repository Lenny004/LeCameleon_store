@extends('layouts.admin')

@section('title', 'Reviews')
@section('page-title', 'Reviews')
@section('page-subtitle', 'Moderate customer feedback')

@section('content')
<form method="GET" action="{{ route('admin.reviews.index') }}" class="card" style="margin-bottom:var(--space-lg);display:grid;gap:var(--space-md);grid-template-columns:repeat(auto-fit,minmax(12rem,1fr));align-items:end;">
    <div class="form-group" style="margin:0;">
        <label class="form-label" for="q">Search</label>
        <input type="search" id="q" name="q" class="form-input" value="{{ $filters['q'] ?? '' }}" placeholder="Product, customer, text…">
    </div>
    <div class="form-group" style="margin:0;">
        <label class="form-label" for="status">Status</label>
        <select id="status" name="status" class="form-select">
            <option value="">All</option>
            <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pending</option>
            <option value="approved" @selected(($filters['status'] ?? '') === 'approved')>Approved</option>
        </select>
    </div>
    <div class="form-group" style="margin:0;">
        <label class="form-label" for="rating">Rating</label>
        <select id="rating" name="rating" class="form-select">
            <option value="">All</option>
            @for ($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" @selected((string) ($filters['rating'] ?? '') === (string) $i)>{{ $i }} ★</option>
            @endfor
        </select>
    </div>
    <div style="display:flex;gap:var(--space-sm);">
        <button type="submit" class="btn btn--primary">Filter</button>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn--ghost">Clear</a>
    </div>
</form>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Customer</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reviews as $review)
                <tr>
                    <td>
                        @if ($review->product)
                            <a href="{{ route('admin.products.show', $review->product) }}">{{ $review->product->name }}</a>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        {{ $review->user?->name ?? '—' }}
                        @if ($review->user?->email)
                            <br><span class="text-muted text-small">{{ $review->user->email }}</span>
                        @endif
                    </td>
                    <td>{{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}</td>
                    <td>
                        @if ($review->title)
                            <strong>{{ $review->title }}</strong><br>
                        @endif
                        <span class="text-muted">{{ Str::limit($review->body, 80) }}</span>
                    </td>
                    <td>
                        <span class="badge badge--{{ $review->is_approved ? 'success' : 'warning' }}">
                            {{ $review->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    </td>
                    <td>{{ $review->created_at?->format('Y-m-d') }}</td>
                    <td>
                        <div class="table__actions" style="display:flex;flex-wrap:wrap;gap:var(--space-xs);">
                            @unless ($review->is_approved)
                                <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn--ghost btn--sm">Approve</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn--ghost btn--sm">Reject</button>
                                </form>
                            @endunless
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn--ghost btn--sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-muted">No reviews match these filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($reviews->hasPages())
    <div style="margin-top:var(--space-lg);">
        {{ $reviews->links() }}
    </div>
@endif
@endsection
