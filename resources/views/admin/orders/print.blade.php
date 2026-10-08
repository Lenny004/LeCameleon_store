<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Hoja de empaque {{ $order->number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="print-sheet">
    <div class="print-sheet__actions" x-data>
        <button type="button" class="btn btn--primary" x-on:click="window.print()">Imprimir</button>
    </div>
    <header class="print-sheet__header">
        <div>
            <h1>Hoja de empaque / Comprobante de pedido</h1>
            <p>{{ $order->number }}</p>
        </div>
        <p>{{ $order->placed_at?->format('d/m/Y H:i') }}</p>
    </header>

    <section class="print-sheet__customer">
        <h2>Cliente</h2>
        <p>{{ $order->user?->name ?? trim(data_get($order->shipping_address, 'first_name').' '.data_get($order->shipping_address, 'last_name')) }}</p>
        <p>{{ $order->customerEmail() }}</p>
        <p>Teléfono: {{ data_get($order->shipping_address, 'phone') ?: 'No indicado' }}</p>
        <p>Municipio: {{ $municipality?->name ?: 'No indicado' }}</p>
        <p>{{ collect($order->shipping_address ?? [])->only(['line1', 'line2', 'city', 'state', 'postal_code', 'country'])->filter()->implode(', ') }}</p>
    </section>

    <table class="print-sheet__table">
        <thead>
            <tr>
                <th class="print-sheet__cell">Artículo</th>
                <th class="print-sheet__cell">SKU</th>
                <th class="print-sheet__cell">Cantidad</th>
                <th class="print-sheet__cell">Precio</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td class="print-sheet__cell">{{ $item->name }}</td>
                    <td class="print-sheet__cell">{{ $item->sku }}</td>
                    <td class="print-sheet__cell">{{ $item->quantity }}</td>
                    <td class="print-sheet__cell">${{ number_format((float) $item->unit_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="print-sheet__total">
        <p>Subtotal: ${{ number_format((float) $order->subtotal, 2) }}</p>
        <p>Descuento: ${{ number_format((float) $order->discount_total, 2) }}</p>
        <p>Envío: ${{ number_format((float) $order->shipping_total, 2) }}</p>
        <h2>Total: ${{ number_format((float) $order->grand_total, 2) }}</h2>
    </div>

    @if ($order->notes)
        <p>Notas del cliente: {{ $order->notes }}</p>
    @endif
    @if ($order->shipments->first()?->tracking_number)
        <p>Rastreo: {{ $order->shipments->first()->tracking_number }}</p>
    @endif
</main>
</body>
</html>
