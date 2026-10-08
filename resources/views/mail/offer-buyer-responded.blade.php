<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Respuesta a una oferta</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>Respuesta a una oferta</h1>
    <p>Hola{{ $offer->user?->name ? ' '.$offer->user->name : '' }},</p>
    <p>El cliente respondió <strong>{{ $response === 'accepted' ? 'aceptando' : 'rechazando' }}</strong> la oferta de {{ $offer->product->name }}.</p>
    <p>Oferta: ${{ number_format((float) $offer->amount, 2) }}</p>
    <p><a href="{{ url('/admin/offers') }}">Ver la oferta en el panel</a></p>
</body>
</html>
