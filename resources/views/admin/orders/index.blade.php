@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'Orders')
@section('page-subtitle', 'Order pipeline and fulfillment')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">All orders</h2>
    </div>
</div>

<div class="table-wrap">
    <table class="table">
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
            @foreach ($orders ?? [
                ['id' => 'LC-1042', 'customer' => 'maria@email.com', 'items' => 2, 'total' => 189, 'status' => 'processing', 'date' => '2026-07-14'],
                ['id' => 'LC-1041', 'customer' => 'carlos@email.com', 'items' => 1, 'total' => 65, 'status' => 'shipped', 'date' => '2026-07-13'],
                ['id' => 'LC-1040', 'customer' => 'ana@email.com', 'items' => 3, 'total' => 245, 'status' => 'delivered', 'date' => '2026-07-12'],
            ] as $order)
                <tr>
                    <td>{{ $order['id'] }}</td>
                    <td>{{ $order['customer'] }}</td>
                    <td>{{ $order['items'] }}</td>
                    <td>${{ number_format($order['total'], 2) }}</td>
                    <td><span class="badge badge--primary">{{ ucfirst($order['status']) }}</span></td>
                    <td>{{ $order['date'] }}</td>
                    <td>
                        @if (Route::has('admin.orders.show'))
                            <a href="{{ route('admin.orders.show', $order['id']) }}" class="btn btn--ghost btn--sm">View</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
