@extends('layouts.admin')

@section('title', 'Coupons')
@section('page-title', 'Coupons')

@section('content')
<div class="admin-page-header">
    <h2 class="admin-page-header__title">Discount codes</h2>
    @if (Route::has('admin.coupons.create'))
        <a href="{{ route('admin.coupons.create') }}" class="btn btn--primary">Create coupon</a>
    @endif
</div>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Applies to</th>
                <th>Uses</th>
                <th>Expires</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($coupons as $coupon)
                <tr>
                    <td><a class="admin-table__link" href="{{ route('admin.coupons.show', $coupon) }}"><code>{{ $coupon->code }}</code></a></td>
                    <td>{{ ucfirst($coupon->type->value) }}</td>
                    <td>{{ $coupon->type->value === 'percent' ? $coupon->value.'%' : '$'.number_format((float) $coupon->value, 2) }}</td>
                    <td>{{ $coupon->shipping_only ? 'Shipping' : 'Order' }}</td>
                    <td>{{ $coupon->used_count }} / {{ $coupon->max_uses ?? '∞' }}</td>
                    <td>{{ $coupon->ends_at?->format('Y-m-d') ?? '—' }}</td>
                    <td>
                        <span class="badge badge--{{ $coupon->isValid() ? 'success' : 'warning' }}">
                            {{ $coupon->isValid() ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.coupons.edit', $coupon) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Delete this coupon?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-muted">No coupons found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $coupons->links() }}
@endsection
