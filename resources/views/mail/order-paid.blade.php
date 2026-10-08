<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment received</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>Payment received</h1>
    <p>We have received payment for order <strong>{{ $order->number }}</strong>.</p>
    <p>Total paid: {{ $order->currency }} {{ number_format((float) $order->grand_total, 2) }}</p>
    <p>We are preparing your items for shipment.</p>
</body>
</html>
