@extends('layouts.store')

@section('title', 'Términos — Le Cameleon')

@section('content')
<div class="container" style="padding-block:var(--space-2xl);max-width:46rem;">
    <h1 class="heading-2" style="margin-bottom:var(--space-lg);">Términos de uso</h1>

    <div class="checkout-section" style="display:flex;flex-direction:column;gap:var(--space-lg);">
        <p class="text-muted">Última actualización: {{ date('Y') }}</p>

        <p>
            Al usar Le Cameleon aceptas estas condiciones básicas de la tienda vintage online.
        </p>

        <h2 class="text-small" style="font-weight:700;">Productos</h2>
        <p class="text-muted">
            Las piezas suelen ser únicas o de stock limitado. La descripción, medidas y grado
            de condición son una guía honesta; el vintage implica señales de uso propias de su época.
        </p>

        <h2 class="text-small" style="font-weight:700;">Pedidos y pagos</h2>
        <p class="text-muted">
            Un pedido se confirma cuando el pago se registra como capturado. Nos reservamos el
            derecho de cancelar pedidos con error de precio o stock no disponible.
        </p>

        <h2 class="text-small" style="font-weight:700;">Ofertas</h2>
        <p class="text-muted">
            Las ofertas (“make an offer”) no son vinculantes hasta que el equipo las acepta.
            Una contraoferta puede expirar sin respuesta.
        </p>

        <h2 class="text-small" style="font-weight:700;">Devoluciones</h2>
        <p class="text-muted">
            Consulta la
            <a href="{{ route('returns') }}">política de devoluciones</a>
            vigente.
        </p>
    </div>
</div>
@endsection
