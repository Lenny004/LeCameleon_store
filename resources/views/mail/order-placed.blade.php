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
@if ($order->paymentInstructions())
<h2>{{ $order->paymentInstructions()['title'] }}</h2>
<ul>@foreach ($order->paymentInstructions()['lines'] as $line)<li>{{ $line }}</li>@endforeach</ul>
@if ($order->paymentInstructions()['extra'])<p>{{ $order->paymentInstructions()['extra'] }}</p>@endif
@if ($order->paymentMethod() === 'transfer')<p>Si eres invitado, puedes cargar tu comprobante durante los próximos 7 días: <a href="{{ $receiptUploadUrl }}">cargar comprobante</a>.</p>@endif
@endif
</body>
</html>
