<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante rechazado</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>Comprobante de pago rechazado</h1>
    <p>Hola{{ $receipt->order->user?->name ? ' '.$receipt->order->user->name : '' }},</p>
    <p>No pudimos aceptar el comprobante del pedido <strong>{{ $receipt->order->number }}</strong>.</p>
    @if ($receipt->admin_notes)
        <p>Nota del equipo: {{ $receipt->admin_notes }}</p>
    @endif
    @if (Route::has('account.orders.show') && $receipt->order->user_id)
        <p><a href="{{ route('account.orders.show', $receipt->order) }}">Cargar otro comprobante</a></p>
    @endif
</body>
</html>
