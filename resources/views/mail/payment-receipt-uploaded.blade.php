<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante recibido</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>Nuevo comprobante de pago</h1>
    <p>Hola{{ $receipt->order->user?->name ? ' '.$receipt->order->user->name : '' }},</p>
    <p>Se recibió un comprobante para el pedido <strong>{{ $receipt->order->number }}</strong>.</p>
    <p>El equipo lo revisará pronto.</p>
    <p><a href="{{ url('/admin/orders/'.$receipt->order->id) }}">Ver el pedido en el panel</a></p>
</body>
</html>
