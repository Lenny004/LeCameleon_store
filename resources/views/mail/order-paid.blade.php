<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Pago recibido</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
<h1>Pago recibido</h1>
<p>Recibimos el pago de tu pedido <strong>{{ $order->number }}</strong>.</p>
<p>Total pagado: {{ $order->currency }} {{ number_format((float) $order->grand_total, 2) }}</p>
<p>Ya estamos preparando tus piezas para el envío.</p>
</body>
</html>
