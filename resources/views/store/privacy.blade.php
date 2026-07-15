@extends('layouts.store')

@section('title', 'Privacidad — Le Cameleon')

@section('content')
<div class="container" style="padding-block:var(--space-2xl);max-width:46rem;">
    <h1 class="heading-2" style="margin-bottom:var(--space-lg);">Política de privacidad</h1>

    <div class="checkout-section" style="display:flex;flex-direction:column;gap:var(--space-lg);">
        <p class="text-muted">Última actualización: {{ date('Y') }}</p>

        <p>
            En Le Cameleon tratamos tus datos con cuidado. Esta página resume qué información
            recopilamos y para qué la usamos en la tienda online.
        </p>

        <h2 class="text-small" style="font-weight:700;">Datos que podemos guardar</h2>
        <ul class="text-muted" style="padding-left:1.25rem;display:flex;flex-direction:column;gap:var(--space-xs);">
            <li>Cuenta: nombre, correo y contraseña (cifrada).</li>
            <li>Pedidos: dirección de envío/facturación y detalle de compra.</li>
            <li>Contacto y boletín: correo y mensaje si nos escribes o te suscribes.</li>
            <li>Navegación técnica: sesión, carrito e identificadores necesarios para el sitio.</li>
        </ul>

        <h2 class="text-small" style="font-weight:700;">Uso</h2>
        <p class="text-muted">
            Procesamos pedidos, respondemos consultas, mejoramos el catálogo y enviamos
            avisos relacionados con tu compra. El boletín solo se usa si te suscribes.
        </p>

        <h2 class="text-small" style="font-weight:700;">Tus derechos</h2>
        <p class="text-muted">
            Puedes solicitar acceso, corrección o eliminación de tus datos escribiendo a
            <a href="{{ route('contact.show') }}">Contacto</a>.
        </p>
    </div>
</div>
@endsection
