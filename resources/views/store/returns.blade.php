@extends('layouts.store')

@section('title', 'Devoluciones — Le Cameleon')

@section('content')
<div class="container content-page">
    <header class="content-page__header">
        <p class="content-page__eyebrow">Políticas</p>
        <h1 class="content-page__title">Política de devoluciones</h1>
        <p class="content-page__intro">
            Seleccionamos piezas vintage únicas. Queremos que estés satisfecho con tu compra.
        </p>
    </header>

    @if (! empty($policyContent))
        <div class="content-page__prose">
            {!! nl2br(e(is_string($policyContent) ? $policyContent : ($policyContent['body'] ?? ''))) !!}
        </div>
    @else
        <div class="content-page__prose">
            <h2>Plazo</h2>
            <p>Puedes solicitar una devolución dentro de los <strong>14 días naturales</strong> posteriores a la entrega.</p>

            <h2>Condiciones</h2>
            <ul>
                <li>La pieza debe estar sin usar, con etiquetas originales si las tenía, y en el mismo estado en que la recibiste.</li>
                <li>Los artículos en oferta final o marcados como pieza única pueden tener restricciones adicionales.</li>
                <li>El costo de envío de devolución corre por cuenta del cliente, salvo error nuestro o defecto no descrito.</li>
            </ul>

            <h2>Cómo solicitar una devolución</h2>
            <p>
                Inicia sesión, ve a <strong>Mis pedidos</strong>, abre el pedido correspondiente y envía el formulario de devolución.
                Revisaremos tu solicitud y te contactaremos por correo con los siguientes pasos.
            </p>

            <h2>Reembolsos</h2>
            <p>
                Una vez aprobada la devolución y recibida la pieza en nuestro almacén, procesaremos el reembolso al método de pago original
                en un plazo de 5 a 10 días hábiles.
            </p>
        </div>
    @endif

    @auth
        @if (Route::has('account.orders.index'))
            <div class="content-page__actions">
                <a href="{{ route('account.orders.index') }}" class="btn btn--primary">Ver mis pedidos</a>
            </div>
        @endif
    @endauth
</div>
@endsection
