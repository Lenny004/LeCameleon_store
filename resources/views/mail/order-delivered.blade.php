<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pedido entregado</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>Tu pedido fue entregado</h1>
    <p>Hola{{ $order->user?->name ? ' '.$order->user->name : '' }},</p>
    <p>El pedido <strong>{{ $order->number }}</strong> fue marcado como entregado. ¡Gracias por comprar en Le Cameleon!</p>
    @if (Route::has('account.orders.show') && $order->user_id)
        <p><a href="{{ route('account.orders.show', $order) }}">Ver el pedido en mi cuenta</a></p>
    @endif
</body>
</html>
