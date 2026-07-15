@extends('layouts.store')

@section('title', 'Devoluciones — Le Cameleon')

@section('content')
<div class="container" style="padding-block:var(--space-xl);max-width:42rem;">
    <h1 class="heading-2" style="margin-bottom:var(--space-lg);">Política de devoluciones</h1>

    @if (! empty($policyContent))
        <div class="checkout-section">
            {!! nl2br(e(is_string($policyContent) ? $policyContent : ($policyContent['body'] ?? ''))) !!}
        </div>
    @else
        <div class="checkout-section" style="display:flex;flex-direction:column;gap:var(--space-md);">
            <p>En Le Cameleon seleccionamos piezas vintage únicas. Queremos que estés satisfecho con tu compra.</p>

            <h2 class="text-small" style="font-weight:700;">Plazo</h2>
            <p class="text-muted">Puedes solicitar una devolución dentro de los <strong>14 días naturales</strong> posteriores a la entrega.</p>

            <h2 class="text-small" style="font-weight:700;">Condiciones</h2>
            <ul class="text-muted" style="padding-left:1.25rem;">
                <li>La pieza debe estar sin usar, con etiquetas originales si las tenía, y en el mismo estado en que la recibiste.</li>
                <li>Los artículos en oferta final o marcados como pieza única pueden tener restricciones adicionales.</li>
                <li>El costo de envío de devolución corre por cuenta del cliente, salvo error nuestro o defecto no descrito.</li>
            </ul>

            <h2 class="text-small" style="font-weight:700;">Cómo solicitar una devolución</h2>
            <p class="text-muted">
                Inicia sesión, ve a <strong>Mis pedidos</strong>, abre el pedido correspondiente y envía el formulario de devolución.
                Revisaremos tu solicitud y te contactaremos por correo con los siguientes pasos.
            </p>

            <h2 class="text-small" style="font-weight:700;">Reembolsos</h2>
            <p class="text-muted">
                Una vez aprobada la devolución y recibida la pieza en nuestro almacén, procesaremos el reembolso al método de pago original
                en un plazo de 5 a 10 días hábiles.
            </p>
        </div>
    @endif

    @auth
        @if (Route::has('account.orders.index'))
            <p style="margin-top:var(--space-xl);">
                <a href="{{ route('account.orders.index') }}" class="btn btn--primary">Ver mis pedidos</a>
            </p>
        @endif
    @endauth
</div>
@endsection
