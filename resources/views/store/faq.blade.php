@extends('layouts.store')

@section('title', 'Preguntas frecuentes — Le Cameleon')

@section('content')
@php
    $items = [
        [
            'question' => '¿Qué significan los grados de condición?',
            'answer' => <<<'HTML'
<p>Cada pieza vintage incluye un grado de condición honesto para que sepas exactamente qué compras:</p>
<ul>
    <li><strong>Mint</strong> — Como nueva o sin uso aparente; sin desgaste visible.</li>
    <li><strong>Excellent</strong> — Muy buen estado; puede tener un desgaste mínimo propio de la época.</li>
    <li><strong>Good</strong> — Uso ligero visible; pequeñas marcas o desgaste acorde a su edad.</li>
    <li><strong>Fair</strong> — Signos claros de uso; ideal si buscas carácter auténtico a mejor precio.</li>
    <li><strong>Poor</strong> — Desgaste notable; describimos todos los detalles en la ficha del producto.</li>
</ul>
<p>Las fotos y la descripción complementan el grado. Si tienes dudas, escríbenos antes de comprar.</p>
HTML,
        ],
        [
            'question' => '¿Cómo leo las medidas de las prendas?',
            'answer' => <<<'HTML'
<p>Las medidas en la ficha del producto son reales, tomadas con la prenda extendida sobre una superficie plana:</p>
<ul>
    <li><strong>Pecho / cintura / cadera</strong> — anchura de pit a pit o contorno, según indiquemos.</li>
    <li><strong>Largo</strong> — desde el hombro o la cintura hasta el bajo.</li>
    <li><strong>Mangas</strong> — desde la costura del hombro hasta el puño.</li>
</ul>
<p>Las tallas de etiqueta vintage no coinciden con las actuales; compara siempre con tus medidas corporales y, si lo necesitas, pregúntanos por una medida adicional.</p>
HTML,
        ],
        [
            'question' => '¿Cómo funcionan los envíos?',
            'answer' => <<<'HTML'
<p>Preparamos cada pedido con embalaje cuidadoso para proteger piezas delicadas. El costo de envío se calcula en el checkout según la tarifa vigente.</p>
<p>Tras confirmar el pago, recibirás un correo con la confirmación. Cuando el paquete salga de nuestro almacén, te enviaremos el número de seguimiento si el transportista lo ofrece.</p>
<p>Los plazos de entrega dependen de tu ubicación; suelen ser de 3 a 7 días hábiles dentro del país una vez despachado el pedido.</p>
HTML,
        ],
        [
            'question' => '¿Cuál es la política de devoluciones?',
            'answer' => <<<'HTML'
<p>Puedes solicitar una devolución dentro de los <strong>14 días naturales</strong> posteriores a la entrega, siempre que la pieza esté en el mismo estado en que la recibiste.</p>
<p>El envío de devolución corre por cuenta del cliente, salvo error nuestro o un defecto no descrito. Consulta los detalles completos en nuestra <a href="ROUTE_RETURNS">página de devoluciones</a>.</p>
HTML,
        ],
        [
            'question' => '¿Cómo verifican la autenticidad?',
            'answer' => <<<'HTML'
<p>En piezas de mayor valor revisamos etiquetas, costuras, materiales y procedencia. Los artículos verificados muestran la insignia <strong>Autenticado</strong> en la ficha del producto.</p>
<p>No todas las piezas requieren verificación formal, pero siempre describimos con honestidad la época, el origen y cualquier señal de reproducción o restauración.</p>
HTML,
        ],
        [
            'question' => '¿Puedo hacer una oferta por un artículo?',
            'answer' => <<<'HTML'
<p>Sí. En productos publicados y con stock disponible, los clientes registrados pueden enviar una <strong>oferta de precio</strong> desde la ficha del producto.</p>
<p>Revisamos cada propuesta y podemos aceptarla, rechazarla o enviarte una contraoferta. Hasta que no confirmes y completes el pago, la pieza sigue disponible para otros compradores.</p>
HTML,
        ],
    ];

    $returnsUrl = Route::has('returns') ? route('returns') : '#';
    $items = array_map(function (array $item) use ($returnsUrl) {
        $item['answer'] = str_replace('ROUTE_RETURNS', $returnsUrl, $item['answer']);

        return $item;
    }, $items);
@endphp

<div class="container" style="padding-block:var(--space-2xl);max-width:42rem;">
    <h1 class="heading-2" style="margin-bottom:var(--space-sm);">Preguntas frecuentes</h1>
    <p class="text-muted" style="margin-bottom:var(--space-xl);">
        Todo lo que necesitas saber antes de comprar vintage: condición, medidas, envíos y más.
    </p>

    <div class="faq" x-data="{ open: 0 }">
        @foreach ($items as $index => $item)
            <div class="faq__item" :class="{ 'faq__item--open': open === {{ $index }} }">
                <button
                    type="button"
                    class="faq__question"
                    @click="open = open === {{ $index }} ? null : {{ $index }}"
                    :aria-expanded="open === {{ $index }}"
                >
                    <span>{{ $item['question'] }}</span>
                    <svg class="faq__icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div
                    class="faq__answer"
                    x-show="open === {{ $index }}"
                    x-cloak
                >
                    {!! $item['answer'] !!}
                </div>
            </div>
        @endforeach
    </div>

    @if (Route::has('contact.show'))
        <p class="text-muted" style="margin-top:var(--space-xl);">
            ¿No encuentras lo que buscas?
            <a href="{{ route('contact.show') }}">Contáctanos</a> y te ayudamos con gusto.
        </p>
    @endif
</div>
@endsection
