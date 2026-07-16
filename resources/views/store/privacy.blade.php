@extends('layouts.store')

@section('title', 'Privacidad — Le Cameleon')

@section('content')
<div class="container content-page">
    <header class="content-page__header">
        <p class="content-page__eyebrow">Legal</p>
        <h1 class="content-page__title">Política de privacidad</h1>
        <p class="content-page__meta">Última actualización: {{ date('Y') }}</p>
    </header>

    <div class="content-page__prose">
        <p>
            En Le Cameleon tratamos tus datos con cuidado. Esta página resume qué información
            recopilamos y para qué la usamos en la tienda online.
        </p>

        <h2>Datos que podemos guardar</h2>
        <ul>
            <li>Cuenta: nombre, correo y contraseña (cifrada).</li>
            <li>Pedidos: dirección de envío/facturación y detalle de compra.</li>
            <li>Contacto y boletín: correo y mensaje si nos escribes o te suscribes.</li>
            <li>Navegación técnica: sesión, carrito e identificadores necesarios para el sitio.</li>
        </ul>

        <h2>Uso</h2>
        <p>
            Procesamos pedidos, respondemos consultas, mejoramos el catálogo y enviamos
            avisos relacionados con tu compra. El boletín solo se usa si te suscribes.
        </p>

        <h2>Tus derechos</h2>
        <p>
            Puedes solicitar acceso, corrección o eliminación de tus datos escribiendo a
            <a href="{{ route('contact.show') }}">Contacto</a>.
        </p>
    </div>
</div>
@endsection
