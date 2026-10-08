<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Tu carrito te espera</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
<h1>¿Sigues pensándolo?</h1>
<p>Hola{{ $cart->user?->name ? ' '.$cart->user->name : '' }}, dejaste {{ $cart->items->count() }} {{ $cart->items->count() === 1 ? 'pieza' : 'piezas' }} en tu carrito de Le Cameleon.</p>
<ul>
@foreach ($cart->items as $item)
<li>{{ $item->product?->name ?? 'Pieza' }} × {{ $item->quantity }}</li>
@endforeach
</ul>
@if (Route::has('cart.index'))
<p><a href="{{ route('cart.index') }}">Vuelve a tu carrito</a> antes de que alguien más se las lleve: son piezas únicas.</p>
@endif
</body>
</html>
