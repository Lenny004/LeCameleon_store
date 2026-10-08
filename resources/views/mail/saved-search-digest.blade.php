<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Nuevas piezas</title></head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>Nuevas piezas para «{{ $savedSearch->name }}»</h1>
    <p>Encontramos novedades que coinciden con tu búsqueda guardada.</p>
    <ul>
        @foreach ($products as $product)
            <li><a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a> — ${{ number_format((float) $product->price, 2) }}</li>
        @endforeach
    </ul>
    <p><a href="{{ route('account.saved-searches.index') }}">Administrar mis avisos</a></p>
</body>
</html>
