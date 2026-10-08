@extends('layouts.admin')

@section('title', 'Cupones')
@section('page-title', 'Cupones')

@section('content')
<div class="admin-page-header">
    <h2 class="admin-page-header__title">Códigos de descuento</h2>
    @if (Route::has('admin.coupons.create'))
        <a href="{{ route('admin.coupons.create') }}" class="btn btn--primary">Crear cupón</a>
    @endif
</div>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Aplica a</th>
                <th>Usos</th>
                <th>Vence</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($coupons as $coupon)
                <tr>
                    <td><a class="admin-table__link" href="{{ route('admin.coupons.show', $coupon) }}"><code>{{ $coupon->code }}</code></a></td>
                    <td>{{ $coupon->type->value === 'percent' ? 'Porcentaje' : 'Monto fijo' }}</td>
                    <td>{{ $coupon->type->value === 'percent' ? $coupon->value.'%' : '$'.number_format((float) $coupon->value, 2) }}</td>
                    <td>{{ $coupon->shipping_only ? 'Envío' : 'Pedido' }}</td>
                    <td>{{ $coupon->used_count }} / {{ $coupon->max_uses ?? '∞' }}</td>
                    <td>{{ $coupon->ends_at?->format('Y-m-d') ?? '—' }}</td>
                    <td>
                        <span class="badge badge--{{ $coupon->isValid() ? 'success' : 'warning' }}">
                            {{ $coupon->isValid() ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.coupons.edit', $coupon) }}">Editar</a>
                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('¿Eliminar este cupón?');" class="admin-inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm" aria-label="Eliminar cupón {{ $coupon->code }}">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-muted">No se encontraron cupones.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $coupons->links() }}
@endsection
