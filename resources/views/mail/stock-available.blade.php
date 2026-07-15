<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Producto disponible</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>¡Buenas noticias!</h1>
    <p><strong>{{ $product->name }}</strong> vuelve a estar disponible en Le Cameleon.</p>
    <p><a href="{{ url('/shop/'.$product->slug) }}">Ver producto</a></p>
</body>
</html>
