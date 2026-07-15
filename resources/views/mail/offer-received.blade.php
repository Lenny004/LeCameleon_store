<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Nueva oferta</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>Nueva oferta recibida</h1>
    <p><strong>{{ $offer->product->name }}</strong></p>
    <p>Precio de lista: ${{ number_format((float) $offer->product->price, 2) }}</p>
    <p>Oferta del cliente: ${{ number_format((float) $offer->amount, 2) }}</p>
    <p>Cliente: {{ $offer->user->name }} ({{ $offer->user->email }})</p>
    @if ($offer->message)
        <p>Mensaje: {{ $offer->message }}</p>
    @endif
    <p><a href="{{ url('/admin/offers') }}">Ver en el panel</a></p>
</body>
</html>
