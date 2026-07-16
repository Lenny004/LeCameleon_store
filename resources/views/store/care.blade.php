@extends('layouts.store')

@section('title', 'Cuidado vintage — Le Cameleon')

@section('content')
{{-- Practical care tips for second-hand garments and objects. --}}
<div class="container content-page">
    <header class="content-page__header">
        <p class="content-page__eyebrow">Guía</p>
        <h1 class="content-page__title">Cuidado de piezas vintage</h1>
        <p class="content-page__intro">
            Las prendas y objetos vintage ya vivieron una vida. Con buen cuidado pueden acompañarte muchos años más.
        </p>
    </header>

    <div class="content-page__prose">
        <h2>Antes del primer uso</h2>
        <ul>
            <li>Revisa costuras, cremalleras y forros con luz natural.</li>
            <li>Prueba la talla con las medidas publicadas (no solo la etiqueta).</li>
            <li>Si hay olores leves de almacén, ventila en sombra 24–48 horas.</li>
        </ul>

        <h2>Lavado y limpieza</h2>
        <ul>
            <li>Prioriza lavado a mano en agua fría o tintorería especializada.</li>
            <li>Evita secadora: el calor acelera el desgaste de fibras antiguas.</li>
            <li>Para cuero y ante, usa productos específicos y paño seco.</li>
            <li>Sedas y lanas delicadas: siempre limpieza profesional si hay duda.</li>
        </ul>

        <h2>Almacenamiento</h2>
        <ul>
            <li>Usa perchas acolchadas para chaquetas y abrigos.</li>
            <li>Guarda lejos de luz solar directa y humedad.</li>
            <li>Introduce bolsas de tela o papel ácido libre; evita plástico hermético.</li>
        </ul>

        <p>
            ¿Dudas sobre una pieza concreta?
            <a href="{{ route('contact.show') }}">Escríbenos</a>
            y te orientamos según material y época.
        </p>
    </div>
</div>
@endsection
