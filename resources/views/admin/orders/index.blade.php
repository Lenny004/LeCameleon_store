@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'Orders')
@section('page-subtitle', 'Order pipeline and fulfillment')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">All orders</h2>
        <p class="admin-page-header__subtitle">{{ $orders->total() }} orders</p>
    </div>
</div>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>
                        @if (Route::has('admin.orders.show'))
                            <a class="admin-table__link" href="{{ route('admin.orders.show', $order) }}">{{ $order->number }}</a>
                        @else
                            {{ $order->number }}
                        @endif
                    </td>
                    <td>{{ $order->customerEmail() ?? $order->user?->email ?? '—' }}</td>
                    <td>{{ $order->items_count }}</td>
                    <td>${{ number_format((float) $order->grand_total, 2) }}</td>
                    <td><span class="badge badge--primary">{{ ucfirst($order->status->value) }}</span></td>
                    <td>{{ $order->placed_at?->format('Y-m-d') ?? '—' }}</td>
                    <td>
                        @if (Route::has('admin.orders.show'))
                            <a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.orders.show', $order) }}">View</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No orders yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($orders->hasPages())
    <div class="admin-pagination">
        {{ $orders->links() }}
    </div>
@endif
@endsection
