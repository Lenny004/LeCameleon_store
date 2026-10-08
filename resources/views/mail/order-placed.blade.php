<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Pedido recibido</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
<h1>¡Gracias por tu pedido!</h1>
<p>Recibimos tu pedido <strong>{{ $order->number }}</strong>.</p>
<p>Total: {{ $order->currency }} {{ number_format((float) $order->grand_total, 2) }}</p>
<p>Te avisaremos cuando confirmemos tu pago.</p>
</body>
</html>
