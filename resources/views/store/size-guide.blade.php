@extends('layouts.store')

@section('title', 'Guía de tallas — Le Cameleon')
@section('meta_description', 'Cómo medir prendas vintage y comparar equivalencias aproximadas de tallas.')

@section('content')
<article class="container content-page size-guide">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a class="breadcrumb__link" href="{{ route('home') }}">Inicio</a>
        <span class="breadcrumb__sep" aria-hidden="true">/</span>
        <span aria-current="page">Guía de tallas</span>
    </nav>
    <header class="content-page__header">
        <p class="content-page__eyebrow">Compra con confianza</p>
        <h1 class="heading-1">Guía de tallas</h1>
        <p class="text-lead">Las medidas de cada pieza están tomadas en plano y expresadas en centímetros.</p>
    </header>

    <section class="content-page__section">
        <h2 class="heading-2">Cómo medir una prenda</h2>
        <p>Extiende la prenda sobre una superficie plana, sin estirarla. Compara las medidas con una prenda tuya que te quede bien.</p>
        <ul class="content-page__list">
            <li><strong>Pecho:</strong> de axila a axila, en línea recta; duplica el resultado si necesitas el contorno.</li>
            <li><strong>Largo:</strong> desde el punto más alto del hombro hasta el borde inferior.</li>
            <li><strong>Hombros:</strong> de costura a costura por la espalda.</li>
            <li><strong>Manga:</strong> desde la costura del hombro hasta el puño.</li>
            <li><strong>Cintura y cadera:</strong> mide de lado a lado y duplica para obtener una referencia de contorno.</li>
            <li><strong>Tiro:</strong> desde la unión de las piernas hasta la parte superior de la cintura.</li>
            <li><strong>Entrepierna:</strong> desde la unión de las piernas hasta el borde del pantalón.</li>
        </ul>
    </section>

    <section class="content-page__section">
        <h2 class="heading-2">Equivalencias aproximadas</h2>
        <p>Las equivalencias son una referencia, no una garantía: una talla vintage suele ser más pequeña que la talla moderna del mismo número.</p>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th scope="col">Letras</th><th scope="col">US</th><th scope="col">EU</th><th scope="col">MX</th></tr></thead>
                <tbody>
                    <tr><th scope="row">XS</th><td>0–2</td><td>32–34</td><td>24–26</td></tr>
                    <tr><th scope="row">S</th><td>4–6</td><td>36–38</td><td>28–30</td></tr>
                    <tr><th scope="row">M</th><td>8–10</td><td>40–42</td><td>32–34</td></tr>
                    <tr><th scope="row">L</th><td>12–14</td><td>44–46</td><td>36–38</td></tr>
                    <tr><th scope="row">XL</th><td>16–18</td><td>48–50</td><td>40–42</td></tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="content-page__section">
        <h2 class="heading-2">Consejos</h2>
        <ul class="content-page__list">
            <li>Prioriza las medidas de la ficha sobre la etiqueta original.</li>
            <li>Las tallas vintage suelen ser más pequeñas y las prendas pueden haber encogido con el tiempo.</li>
            <li>Compara siempre con una prenda tuya y considera el corte y la elasticidad del material.</li>
        </ul>
    </section>
</article>
@endsection
