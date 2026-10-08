<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Oferta por vencer</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>Tu precio acordado está por vencer</h1>
    <p>Hola{{ $offer->user?->name ? ' '.$offer->user->name : '' }},</p>
    <p>Tu precio acordado para <strong>{{ $offer->product->name }}</strong> vence el {{ $offer->expires_at?->format('d/m/Y H:i') }}.</p>
    <p><a href="{{ url('/account/offers') }}">Ver mis ofertas</a></p>
</body>
</html>
