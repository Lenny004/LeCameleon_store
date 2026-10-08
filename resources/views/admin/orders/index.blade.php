@extends('layouts.admin')

@section('title', 'Pedidos')
@section('page-title', 'Pedidos')
@section('page-subtitle', 'Flujo y preparación de pedidos')

@section('content')
<form method="GET" action="{{ route('admin.orders.index') }}" class="admin-form admin-form--filters">
    <div class="form-row form-row--cols-2">
        <div class="form-group">
            <label class="form-label" for="orders_search">Buscar</label>
            <input id="orders_search" class="form-input" type="search" name="search" value="{{ request('search') }}" placeholder="Número, nombre o correo" maxlength="100">
        </div>
        <div class="form-group">
            <label class="form-label" for="orders_status">Estado</label>
            <select id="orders_status" class="form-select" name="status">
                <option value="">Todos los estados</option>
                @foreach (\App\Enums\OrderStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-row form-row--cols-2">
        <div class="form-group">
            <label class="form-label" for="orders_payment_status">Estado de pago</label>
            <select id="orders_payment_status" class="form-select" name="payment_status">
                <option value="">Todos los pagos</option>
                @foreach (\App\Enums\PaymentStatus::cases() as $paymentStatus)
                    <option value="{{ $paymentStatus->value }}" @selected(request('payment_status') === $paymentStatus->value)>{{ $paymentStatus->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" for="orders_payment_method">Método de pago</label>
            <select id="orders_payment_method" class="form-select" name="payment_method">
                <option value="">Todos los métodos</option>
                @foreach (\App\Enums\PaymentMethod::cases() as $paymentMethod)
                    <option value="{{ $paymentMethod->value }}" @selected(request('payment_method') === $paymentMethod->value)>{{ $paymentMethod->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-row form-row--cols-2">
        <div class="form-group">
            <label class="form-label" for="orders_from">Desde</label>
            <input id="orders_from" class="form-input" type="date" name="from" value="{{ request('from') }}">
        </div>
        <div class="form-group">
            <label class="form-label" for="orders_to">Hasta</label>
            <input id="orders_to" class="form-input" type="date" name="to" value="{{ request('to') }}">
        </div>
    </div>
    <div class="admin-form__actions">
        <button class="btn btn--ghost" type="submit">Filtrar</button>
        <a class="btn btn--ghost" href="{{ route('admin.orders.index') }}">Limpiar</a>
        <a class="btn btn--ghost" href="{{ route('admin.orders.export', request()->query()) }}">Exportar CSV</a>
        <a class="btn btn--primary" href="{{ route('admin.orders.create') }}">Nuevo pedido</a>
    </div>
</form>

<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Todos los pedidos</h2>
        <p class="admin-page-header__subtitle">{{ $orders->total() }} pedidos</p>
    </div>
</div>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Pedido</th>
                <th>Cliente</th>
                <th>Artículos</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td><a class="admin-table__link" href="{{ route('admin.orders.show', $order) }}">{{ $order->number }}</a></td>
                    <td>{{ $order->customerEmail() ?? $order->user?->email ?? '—' }}</td>
                    <td>{{ $order->items_count }}</td>
                    <td>${{ number_format((float) $order->grand_total, 2) }}</td>
                    <td><span class="badge badge--primary">{{ $order->status->label() }}</span></td>
                    <td>{{ $order->placed_at?->format('Y-m-d') ?? '—' }}</td>
                    <td><a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.orders.show', $order) }}">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="7">Aún no hay pedidos.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($orders->hasPages())
    <div class="admin-pagination">{{ $orders->links() }}</div>
@endif
@endsection
