<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order placed</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>Thank you for your order</h1>
    <p>Your order <strong>{{ $order->number }}</strong> has been placed.</p>
    <p>Total: {{ $order->currency }} {{ number_format((float) $order->grand_total, 2) }}</p>
    <p>We will notify you when your payment is confirmed.</p>
</body>
</html>
