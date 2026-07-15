<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Respuesta a tu oferta</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>Respuesta a tu oferta</h1>
    <p><strong>{{ $offer->product->name }}</strong></p>
    <p>Tu oferta: ${{ number_format((float) $offer->amount, 2) }}</p>

    @if ($offer->status->value === 'accepted')
        <p>¡Buenas noticias! Aceptamos tu oferta de ${{ number_format((float) $offer->amount, 2) }}.</p>
    @elseif ($offer->status->value === 'declined')
        <p>En esta ocasión no podemos aceptar tu oferta.</p>
    @elseif ($offer->status->value === 'countered')
        <p>Te proponemos un precio de ${{ number_format((float) $offer->counter_amount, 2) }}.</p>
    @endif

    @if ($offer->admin_notes)
        <p>Notas: {{ $offer->admin_notes }}</p>
    @endif

    <p><a href="{{ url('/shop/'.$offer->product->slug) }}">Ver producto</a></p>
    <p><a href="{{ url('/account/offers') }}">Ver mis ofertas</a></p>
</body>
</html>
