@extends('layouts.store')

@section('title', 'Envíos — Le Cameleon')

@section('content')
{{-- Shipping policy summary for customers before checkout. --}}
<div class="container content-page">
    <header class="content-page__header">
        <p class="content-page__eyebrow">Logística</p>
        <h1 class="heading-2">Envíos</h1>
    </header>

    <div class="content-page__prose">
        <p class="text-lead">
            Empacamos cada pieza como si fuera irreemplazable — porque muchas lo son.
        </p>

        <h2>Costos y tiempos</h2>
        <ul>
            <li>Tarifa plana de envío mostrada en el checkout (configurable por la tienda).</li>
            <li>Preparación habitual: 1–3 días hábiles tras confirmar el pago.</li>
            <li>El tiempo en tránsito depende del destino y el transportista.</li>
        </ul>

        <h2>Seguimiento</h2>
        <p class="text-muted">
            Cuando tu pedido se envía, verás transportista y número de guía en
            <a href="{{ route('account.orders.index') }}">Mis pedidos</a>
            (si iniciaste sesión) o en el correo de confirmación.
        </p>

        <h2>Embalaje</h2>
        <ul>
            <li>Protección extra en piezas frágiles u objetos decorativos.</li>
            <li>Etiquetado discreto; no anunciamos el contenido en el exterior.</li>
        </ul>

        <p>
            Más detalles sobre cambios y devoluciones en nuestra
            <a href="{{ route('returns') }}">política de devoluciones</a>.
        </p>
    </div>
</div>
@endsection
