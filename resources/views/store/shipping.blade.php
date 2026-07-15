@extends('layouts.store')

@section('title', 'Envíos — Le Cameleon')

@section('content')
{{-- Shipping policy summary for customers before checkout. --}}
<div class="container" style="padding-block:var(--space-2xl);max-width:46rem;">
    <p class="text-small text-muted" style="letter-spacing:0.12em;text-transform:uppercase;margin-bottom:var(--space-sm);">Logística</p>
    <h1 class="heading-2" style="margin-bottom:var(--space-lg);">Envíos</h1>

    <div class="checkout-section" style="display:flex;flex-direction:column;gap:var(--space-lg);">
        <p class="text-lead">
            Empacamos cada pieza como si fuera irreemplazable — porque muchas lo son.
        </p>

        <h2 class="text-small" style="font-weight:700;">Costos y tiempos</h2>
        <ul class="text-muted" style="padding-left:1.25rem;display:flex;flex-direction:column;gap:var(--space-xs);">
            <li>Tarifa plana de envío mostrada en el checkout (configurable por la tienda).</li>
            <li>Preparación habitual: 1–3 días hábiles tras confirmar el pago.</li>
            <li>El tiempo en tránsito depende del destino y el transportista.</li>
        </ul>

        <h2 class="text-small" style="font-weight:700;">Seguimiento</h2>
        <p class="text-muted">
            Cuando tu pedido se envía, verás transportista y número de guía en
            <a href="{{ route('account.orders.index') }}">Mis pedidos</a>
            (si iniciaste sesión) o en el correo de confirmación.
        </p>

        <h2 class="text-small" style="font-weight:700;">Embalaje</h2>
        <ul class="text-muted" style="padding-left:1.25rem;display:flex;flex-direction:column;gap:var(--space-xs);">
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
