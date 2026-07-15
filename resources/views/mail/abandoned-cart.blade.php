<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Your cart is waiting</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>Still thinking it over?</h1>
    <p>Hi{{ $cart->user?->name ? ' '.$cart->user->name : '' }}, you left {{ $cart->items->count() }} item(s) in your cart at Le Cameleon.</p>

    <ul>
        @foreach ($cart->items as $item)
            <li>{{ $item->product?->name ?? 'Item' }} × {{ $item->quantity }}</li>
        @endforeach
    </ul>

    @if (Route::has('cart.index'))
        <p><a href="{{ route('cart.index') }}">Return to your cart</a> before they are gone.</p>
    @endif
</body>
</html>
