@extends('layouts.admin')

@section('title', $coupon->code . ' — Coupons')
@section('page-title', $coupon->code)

@section('content')
<div class="card admin-panel admin-panel--narrow">
    <p><strong>Code:</strong> <code>{{ $coupon->code }}</code></p>
    <p><strong>Tipo:</strong> {{ ucfirst($coupon->type->value) }}</p>
    <p><strong>Value:</strong> {{ $coupon->type->value === 'percent' ? $coupon->value . '%' : '$' . number_format((float) $coupon->value, 2) }}</p>
    <p><strong>Aplica a:</strong> {{ $coupon->shipping_only ? 'Solo envío' : 'Pedido completo' }}</p>
    <p><strong>Min order:</strong> {{ $coupon->min_order_amount ? '$' . number_format((float) $coupon->min_order_amount, 2) : '—' }}</p>
    <p><strong>Uses:</strong> {{ $coupon->used_count }}{{ $coupon->max_uses ? ' / ' . $coupon->max_uses : ' / ∞' }}</p>
    <p><strong>Starts:</strong> {{ $coupon->starts_at?->format('Y-m-d H:i') ?? '—' }}</p>
    <p><strong>Ends:</strong> {{ $coupon->ends_at?->format('Y-m-d H:i') ?? '—' }}</p>
    <p><strong>Estado:</strong> <span class="badge badge--{{ $coupon->is_active ? 'success' : 'warning' }}">{{ $coupon->is_active ? 'Activo' : 'Inactivo' }}</span></p>
    <div class="admin-actions admin-actions--spaced">
        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn--primary">Editar</a>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn--ghost">Volver</a>
    </div>
</div>
@endsection
