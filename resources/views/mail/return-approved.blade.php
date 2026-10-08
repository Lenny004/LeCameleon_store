<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Devolución aprobada</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>Devolución aprobada</h1>
    <p>Hola{{ $returnRequest->user?->name ? ' '.$returnRequest->user->name : '' }},</p>
    <p>Tu solicitud para el pedido <strong>{{ $returnRequest->order->number }}</strong> fue aprobada.</p>
    @if ($returnRequest->admin_notes)<p>Nota del equipo: {{ $returnRequest->admin_notes }}</p>@endif
    @if (Route::has('account.orders.show'))<p><a href="{{ route('account.orders.show', $returnRequest->order) }}">Ver el pedido</a></p>@endif
</body>
</html>
