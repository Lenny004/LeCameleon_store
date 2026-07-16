@extends('layouts.store')

@section('title', 'Términos — Le Cameleon')

@section('content')
<div class="container content-page">
    <header class="content-page__header">
        <p class="content-page__eyebrow">Legal</p>
        <h1 class="content-page__title">Términos de uso</h1>
        <p class="content-page__meta">Última actualización: {{ date('Y') }}</p>
    </header>

    <div class="content-page__prose">
        <p>
            Al usar Le Cameleon aceptas estas condiciones básicas de la tienda vintage online.
        </p>

        <h2>Productos</h2>
        <p>
            Las piezas suelen ser únicas o de stock limitado. La descripción, medidas y grado
            de condición son una guía honesta; el vintage implica señales de uso propias de su época.
        </p>

        <h2>Pedidos y pagos</h2>
        <p>
            Un pedido se confirma cuando el pago se registra como capturado. Nos reservamos el
            derecho de cancelar pedidos con error de precio o stock no disponible.
        </p>

        <h2>Ofertas</h2>
        <p>
            Las ofertas (“make an offer”) no son vinculantes hasta que el equipo las acepta.
            Una contraoferta puede expirar sin respuesta.
        </p>

        <h2>Devoluciones</h2>
        <p>
            Consulta la
            <a href="{{ route('returns') }}">política de devoluciones</a>
            vigente.
        </p>
    </div>
</div>
@endsection
